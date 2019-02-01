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

use Blackbird\ContentManager\Block\Content\Widget\ContentList as WidgetContentList;

/**
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Block
 */
class ContentList extends WidgetContentList
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_sourceLayouts;

    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Blackbird\ContentManager\Model\RuleFactory $ruleFactory
     * @param \Blackbird\ContentManager\Model\Rule\Condition\Sql\Builder $sqlBuilder
     * @param \Blackbird\ContentManager\Helper\ContentList\Widget\AttributeShow $attributeShowHelper
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $sourceLayouts
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Blackbird\ContentManager\Model\RuleFactory $ruleFactory,
        \Blackbird\ContentManager\Model\Rule\Condition\Sql\Builder $sqlBuilder,
        \Blackbird\ContentManager\Helper\ContentList\Widget\AttributeShow $attributeShowHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $sourceLayouts,
        array $data = []
    ) {
        $this->_sourceLayouts = $sourceLayouts;
        parent::__construct(
            $context,
            $contentCollectionFactory,
            $contentTypeCollectionFactory,
            $customFieldCollectionFactory,
            $conditionsHelper,
            $ruleFactory,
            $sqlBuilder,
            $attributeShowHelper,
            $filterProvider,
            $data
        );
    }

    /**
     * Retrieve how many contents should be displayed
     *
     * @return int
     */
    public function getLimitDisplay()
    {
        $contentList = $this->getContentList();

        if (!$contentList->hasData('limit_display')) {
            $this->setData('limit_display', self::DEFAULT_CONTENTS_COUNT);
        }

        return $contentList->getData('limit_display');
    }

    /**
     * Get current content list
     *
     * @return \Blackbird\ContentManager\Model\ContentList
     */
    public function getContentList()
    {
        if (!$this->hasData('content_list')) {
            $contentList = $this->_coreRegistry->registry('current_contentlist');
            $this->setData('content_list', $contentList);
            $this->setData('content_type', $contentList->getContentType());
        }

        return $this->getData('content_list');
    }

    /**
     * Retrieve how many contents should be displayed per page
     *
     * @return int
     */
    public function getLimitPerPage()
    {
        $contentList = $this->getContentList();

        if (!$contentList->hasData('limit_per_page')) {
            $this->setData('limit_per_page', self::DEFAULT_CONTENTS_PER_PAGE);
        }

        return $contentList->getData('limit_per_page');
    }

    /**
     * Return the pager position
     *
     * @return int
     */
    public function getPagerPosition()
    {
        $res = false;

        if ($this->hasPager()) {
            $res = $this->getContentList()->getData('pager_position');
        }

        return $res;
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function hasPager()
    {
        $contentList = $this->getContentList();

        if (!$contentList->hasData('pager')) {
            $this->setData('pager', self::DEFAULT_SHOW_PAGER);
        }

        return (bool)$contentList->getData('pager');
    }

    /**
     * Returns the conditions
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditions()
    {
        $conditions = $this->getData('conditions_encoded') ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $this->_rule->setConditionsSerialized($conditions);
        }

        return $this->_rule->getConditions();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = parent::getIdentities();
        $identities[] = \Blackbird\ContentManager\Model\ContentList::CACHE_TAG . '_' . $this->getContentList()->getId();

        return $identities;
    }

    /**
     * Retrieve the above content
     *
     * @return string
     */
    public function getTextBefore()
    {
        return $this->getProcessedData($this->getContentList()->getTextBefore());
    }

    /**
     * Retrieve the below content
     *
     * @return string
     */
    public function getTextAfter()
    {
        return $this->getProcessedData($this->getContentList()->getTextAfter());
    }

    /**
     * Retrieve the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getContentList()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        // Init data
        $contentList = $this->_coreRegistry->registry('current_contentlist');

        if ($contentList) {
            $this->setData('content_list', $contentList);
            $this->setData('content_type', $contentList->getContentType());
            $this->setData('order_field', $contentList->getOrderField());
            $this->setData('sort_order', $contentList->getSortOrder());
            $this->setData('conditions', $contentList->getConditions());
        }

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $contentList = $this->getContentList();
        $contentType = $this->getContentType();

        if ($contentList && $contentType && !$this->getTemplate()) {
            // Applied content layout in cascading
            if ($contentList->getLayout() == 0) {
                // Test applying list-"ID".phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/list-' . $contentList->getId() . '.phtml');

                if (!$this->getTemplateFile()) {
                    // Test applying the overriding content type list.phtml
                    $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/list.phtml');

                    if (!$this->getTemplateFile()) {
                        // Applying default list.phtml
                        $this->setTemplate('Blackbird_ContentManager::content/view/default/list.phtml');
                    }
                }
            } else {
                // Test applying view/layout-ID.phtml
                if ($this->_sourceLayouts->layoutExists($contentList->getLayout())) {
                    // Build the layout template dynamically
                    $this->addChild('contentlist_view_layout', \Blackbird\ContentManager\Block\View\Layout::class, [
                        'layout_template' => $this->_sourceLayouts->retrieveLayout($contentList->getLayout()),
                        'content_type' => $contentList,
                    ]);
                }

                // Applying default list.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/list.phtml');
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve the content type
     *
     * @return \Blackbird\ContentManager\Model\ContentType
     */
    public function getContentType()
    {
        if (!$this->hasData('content_type')) {
            $contentTypeCollection = parent::getContentType();

            if ($contentTypeCollection && $contentTypeCollection->count()) {
                $this->setData('content_type', $contentTypeCollection->getFirstItem());
            }
        }

        return $this->getData('content_type');
    }
}
