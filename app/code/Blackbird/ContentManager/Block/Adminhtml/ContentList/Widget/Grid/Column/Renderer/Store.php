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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Renderer;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * StoreView grid column
 *
 * Class Store
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Renderer
 */
class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * Store constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        array $data = []
    ) {
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
        parent::__construct($context, $systemStore, $data);
    }

    /**
     * Render row store views
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $contentList = $this->getContentList($row->getData(ContentListData::ID));
        $origStores = [];
        foreach ($contentList->getStores() as $store) {
            $origStores[] = $store;
        }
        $row->setData($this->getColumn()->getIndex(), $origStores);

        return parent::render($row);
    }

    /**
     * Retrieve the content list from the collection
     *
     * @param int $contentListId
     * @return \Blackbird\ContentManager\Model\Content|null
     */
    protected function getContentList($contentListId)
    {
        $contentList = null;
        $collection = $this->_contentListCollectionFactory->create()
            ->addFieldToFilter(ContentListData::ID, $contentListId);

        if ($collection->count()) {
            /** @var \Blackbird\ContentManager\Model\Content $contentList */
            $contentList = $collection->getFirstItem();
        }

        return $contentList;
    }
}
