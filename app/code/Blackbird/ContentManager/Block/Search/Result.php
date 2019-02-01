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

namespace Blackbird\ContentManager\Block\Search;

use Blackbird\ContentManager\Model\ResourceModel\Content\Collection;
use Blackbird\ContentManager\Model\ResourceModel\Indexer\Fulltext\CollectionFactory as ContentCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Helper\Data;
use Magento\Search\Model\QueryFactory;

/**
 * Content search result block
 *
 * Class Result
 *
 * @package Blackbird\ContentManager\Block\Search
 */
class Result extends Template
{
    const CONTENT_LIST_BLOCK_ID = 'search_content_result_list';

    /**
     * @var QueryFactory
     */
    protected $_queryFactory;

    /**
     * @var Data
     */
    protected $_searchDataHelper;

    /**
     * @var ContentCollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var Collection
     */
    protected $_contentCollection = null;

    /**
     * Result constructor
     *
     * @param Context $context
     * @param QueryFactory $queryFactory
     * @param Data $searchDataHelper
     * @param ContentCollectionFactory $contentCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        QueryFactory $queryFactory,
        Data $searchDataHelper,
        ContentCollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->_queryFactory = $queryFactory;
        $this->_searchDataHelper = $searchDataHelper;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getContentListHtml()
    {
        return $this->getChildHtml(self::CONTENT_LIST_BLOCK_ID);
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock(self::CONTENT_LIST_BLOCK_ID)->getChildHtml('additional');
    }

    /**
     * Retrieve search result count
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $productResults = $this->getSearchResultBlock()->getResultCount();
            $size = $this->getContentCollection()->getSize() + $productResults;
            $this->getQuery()->saveNumResults($size);
            $this->setData('result_count', $size);
        }

        return $this->getData('result_count');
    }

    /**
     * Retrieve the Search Result Block
     *
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchResultBlock()
    {
        return $this->getLayout()->getBlock('search.result');
    }

    /**
     * Retrieve loaded content collection
     *
     * @return Collection
     */
    public function getContentCollection()
    {
        return $this->getListBlock()->getCollection();
    }

    /**
     * Retrieve search content list toolbar block
     *
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getListBlock()
    {
        return $this->getChildBlock(self::CONTENT_LIST_BLOCK_ID);
    }

    /**
     * Retrieve query model object
     *
     * @return \Magento\Search\Model\Query
     */
    public function getQuery()
    {
        return $this->_queryFactory->get();
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getNoResultText()
    {
        if ($this->_searchDataHelper->isMinQueryLength()) {
            return __('Minimum Search query length is %1', $this->getQuery()->getMinQueryLength());
        }

        return $this->getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return $this->_searchDataHelper->getNoteMessages();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->prepareContentList();

        return parent::_prepareLayout();
    }

    /**
     * Prepare the content list
     *
     * @return void
     */
    protected function prepareContentList()
    {
        // Retrieve the entity id to select
        $contentCollection = $this->_contentCollectionFactory->create();
        $contentCollection->addSearchFilter($this->getQuery()->getQueryText());

        if ($contentCollection->getSize()) {
            $this->getListBlock()->addAttributeToFilter('entity_id', 'in', $contentCollection->getAllIds());
        } else {
            $this->getListBlock()->addAttributeToFilter('entity_id', '=', -1);
        }
    }
}
