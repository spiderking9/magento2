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
use Blackbird\ContentManager\Api\Data\FlagInterface;

/**
 * Flag grid column
 *
 * Class Flag
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Renderer
 */
class Flag extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory
     */
    protected $_flagCollectionFactory;

    /**
     * Flag constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        array $data = []
    ) {
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
        $this->_flagCollectionFactory = $flagCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Render row store views flags
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '';
        $data = $row->getData();
        $contentList = $this->getContentList($data[ContentListData::ID]);

        if ($contentList) {
            $flags = $this->getFlags($contentList->getStores());
            foreach ($flags as $flag) {
                $html .= '<div><img src="' . $this->getViewFileUrl(FlagInterface::FLAG_PATH) . '/' . $flag->getValue() . '" class="store-flag-icon" alt="' . $flag->getValue() . '" /></div>';
            }
        }

        return $html;
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

    /**
     * Retrieves the stores flag
     *
     * @param array|int $storeIds
     * @return \Blackbird\ContentManager\Model\ResourceModel\Flag\Collection
     */
    protected function getFlags($storeIds)
    {
        return $this->_flagCollectionFactory->create()->addFieldToFilter(FlagInterface::ID, $storeIds);
    }
}
