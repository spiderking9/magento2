<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_ContentManager
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Block;

use Blackbird\ContentManager\Api\Data\ContentListInterface;
use Blackbird\ContentManager\Api\Data\ContentListInterfaceFactory;
use Blackbird\ContentManager\Block\Content\Widget\ContentList as ContentListWidget;
use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory as ContentCollectionFactory;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory as ContentTypeCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Filter
 *
 * @package Blackbird\ContentManager\Block
 * @method string getRelatedBlockName
 * @method string getFilterDirect
 * @method Filter setFilterDirect(string $direct)
 * @method Filter setFilterPath(string $path)
 * @method Filter setFilters(array $filters)
 * @method Filter setCtType(string $ctType)
 * @method Filter setCtId(int $ctId)
 * @method Filter setClId(int $ctId)
 */
class Filter extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_contentTypeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/view/filter.phtml';

    /**
     * @var ContentListInterfaceFactory
     */
    private $contentListFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\ContentManager\Api\Data\ContentListInterfaceFactory $contentListFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ContentListInterfaceFactory $contentListFactory,
        ContentTypeCollectionFactory $contentTypeCollectionFactory,
        ContentCollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->contentListFactory = $contentListFactory;
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Set the related block name (must be an instance of \Blackbird\ContentManager\Block\Content\Widget\ContentList)
     *
     * @param string $name
     * @return $this
     * @throws LocalizedException
     */
    public function setRelatedBlockName($name)
    {
        $relatedBlock = $this->getLayout()->getBlock($name);
        if ($relatedBlock && !$relatedBlock instanceof ContentListWidget) {
            throw new \InvalidArgumentException(
                __('The related block should be an instance of %1.', ContentListWidget::class)
            );
        }

        return $this->setData('related_block_name', $name);
    }

    /**
     * Retrieve the block filter title
     *
     * @return string
     */
    public function getTitle()
    {
        if (!$this->hasData('title')) {
            $this->setData('title', __('Filter By'));
        }

        return $this->getData('title');
    }

    /**
     * Retrieve the block filter subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        if (!$this->hasData('subtitle')) {
            $this->setData('subtitle', __('Filter Options'));
        }

        return $this->getData('subtitle');
    }

    /**
     * Get the filter url for a custom field filter
     *
     * @param string $filterIdentifier
     * @param string $filterValue
     * @return string
     */
    public function getFilterUrl($filterIdentifier, $filterValue)
    {
        $params = $this->getFilterParams();
        $params['_query'] = [$filterIdentifier => $filterValue];

        return $this->getUrl($this->getFilterPath(), $params);
    }

    /**
     * Retrieve the extra params of the filter url
     *
     * @todo Exclude the page var name
     * @return array
     */
    public function getFilterParams()
    {
        if (!$this->hasData('filter_params')) {
            $params = [
                '_current' => true,
                '_use_rewrite' => true,
            ];

            if ($this->getFilterDirect()) {
                $params['_direct'] = $this->getFilterDirect();
            }

            $this->setData('filter_params', $params);
        }

        return $this->getData('filter_params');
    }

    /**
     * Retrieve the filter path to redirect where
     *
     * @return string
     */
    public function getFilterPath()
    {
        if ($this->getFilterDirect() && $this->hasData('filter_path')) {
            $this->setData('filter_path', '');
        } elseif (!$this->getFilterDirect() && !$this->hasData('filter_path')) {
            $this->setData('filter_path', '*/*/*');
        }

        return $this->getData('filter_path');
    }

    /**
     * Get the remove url for a custom field filter
     *
     * @param string $filterIdentifier
     * @return string
     */
    public function getRemoveUrl($filterIdentifier)
    {
        $params = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$filterIdentifier => null],
            '_escape' => true,
        ];

        return $this->getUrl('*/*/*', $params);
    }

    /**
     * Get url for 'Clear All' link
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];

        foreach ($this->getActiveFilters() as $filter) {
            $filterState[$filter->getIdentifier()] = null;
        }

        $params = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => $filterState,
            '_escape' => true,
        ];

        return $this->getUrl('*/*/*', $params);
    }

    /**
     * Retrieve the active filters
     *
     * @return \Blackbird\ContentManager\Model\ContentType\CustomField[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getActiveFilters()
    {
        if (!$this->hasData('active_filters') && $this->getFilters() && $this->getFilters()->count()) {
            $activeFilters = [];

            /** @var CustomField $filter */
            foreach ($this->getFilters() as $filter) {
                if ($this->isFilterActive($filter->getIdentifier())) {
                    $value = $filter->getOptionText($this->getRequest()->getParam($filter->getIdentifier()));

                    if ($value) {
                        $filter->setValue($value);
                        $activeFilters[] = $filter;
                    }
                }
            }

            $this->setData('active_filters', $activeFilters);
        }

        return $this->getData('active_filters');
    }

    /**
     * Retrieve the filters as CustomField objects
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getFilters()
    {
        if (!$this->hasData('loaded_filters') && $this->hasData('filters')) {
            $filters = $this->getContentType()
                ->getCustomFieldCollection()
                ->addFieldToFilter(CustomField::IDENTIFIER, array_values($this->getData('filters')));

            $this->setData('loaded_filters', $filters);
        }

        return $this->getData('loaded_filters');
    }

    /**
     * Retrieve the content type
     *
     * @return ContentType
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getContentType()
    {
        if (!$this->hasData('content_type') && ($this->hasData('ct_type') || $this->hasData('ct_id'))) {
            $ctIdentifier = null;
            $contentType = null;
            $contentTypeCollection = $this->_contentTypeCollectionFactory->create();

            // Load the content type
            if ($this->hasData('ct_type')) {
                $ctIdentifier = $this->getData('ct_type');
                $contentTypeCollection->addFieldToFilter(ContentType::IDENTIFIER, $ctIdentifier);
            }
            if (!$ctIdentifier && $this->hasData('ct_id')) {
                $ctIdentifier = $this->getData('ct_id');
                $contentTypeCollection->addFieldToFilter(ContentType::ID, $ctIdentifier);
            }
            if ($ctIdentifier) {
                $this->setData(
                    'content_type',
                    $contentTypeCollection->count() ? $contentTypeCollection->getFirstItem() : null
                );
            }
        } elseif ($this->getContentList()) {
            $this->setData('content_type', $this->getContentList()->getContentType());
        } elseif ($this->getRelatedBlockName()) {
            $this->setData(
                'content_type',
                $this->getLayout()->getBlock($this->getRelatedBlockName())->getContentType()
            );
        }

        return $this->getData('content_type');
    }

    /**
     * Retrieve the content list
     *
     * @return ContentListInterface
     * @throws NoSuchEntityException
     */
    public function getContentList()
    {
        if (!$this->hasData('content_list') && $this->hasData('cl_id')) {
            $contentList = $this->contentListFactory->create()->load($this->getData('cl_id'));
            // todo refactor with the repository, which is already to do... O:)
            if (!$contentList->getId()) {
                throw new NoSuchEntityException(__('The Content List does not exists.'));
            }

            $this->setData('content_list', $contentList);
        }

        return $this->getData('content_list');
    }

    /**
     * Check if a filter is active (by its value: optional)
     *
     * @param string $filterIdentifier
     * @param null|mixed $filterValue
     * @return bool
     */
    public function isFilterActive($filterIdentifier, $filterValue = null)
    {
        $isFilterActive = !empty($this->getRequest()->getParam($filterIdentifier));

        if ($isFilterActive && !is_null($filterValue)) {
            $isFilterActive = ($this->getRequest()->getParam($filterIdentifier) == $filterValue);
        }

        return $isFilterActive;
    }

    /**
     * Check is there's active filters
     *
     * @return bool
     */
    public function hasActiveFilters()
    {
        return !empty($this->getActiveFilters());
    }

    /**
     * Retrieve the total count of potential results for a filter
     *
     * @param string $filterIdentifier
     * @param string $filterValue
     * @return int
     * @throws LocalizedException
     */
    public function getResultFilterCount($filterIdentifier, $filterValue)
    {
        $collection = clone $this->getCollection();
        $collection->clear()->addAttributeToFilter($filterIdentifier, ['finset' => $filterValue]);

        return $collection->count();
    }

    /**
     * Get Content Collection
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     * @throws LocalizedException
     */
    protected function getCollection()
    {
        if (!$this->hasData('collection')) {
            if ($this->getRelatedBlockName()) {
                $collection = clone $this->getLayout()->getBlock($this->getRelatedBlockName())->getCollection();
            } elseif ($this->getContentList()) {
                $collection = $this->getContentList()->getContentCollection(true);
            } elseif ($this->getContentType()) {
                $collection = $this->_contentCollectionFactory->create()->addContentTypeFilter($this->getContentType());
            } else {
                throw new LocalizedException(__('Data not found.'));
            }
            $collection->addStoreFilter()->addAttributeToFilter(Content::STATUS, 1);

            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        // Load the collection
        $this->getFilters()->load();

        return parent::_beforeToHtml();
    }
}
