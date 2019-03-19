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

namespace Blackbird\ContentManager\Controller\Index;

use Blackbird\ContentManager\Model\ContentList as ContentListModel;

/**
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Controller\Index
 */
class ContentList extends \Blackbird\ContentManager\Controller\Index\View
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * ContentList constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Blackbird\ContentManager\Helper\ContentList\View $helperView
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param string $paramName
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blackbird\ContentManager\Helper\ContentList\View $helperView,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        $paramName = 'contentlist_id'
    ) {
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
        parent::__construct($context, $storeManager, $helperView, $resultForwardFactory, $resultPageFactory,
            $paramName);
    }

    /**
     * Check if the object exists
     *
     * @param int $dataId
     * @return boolean
     */
    protected function isObjectExists($dataId)
    {
        return $this->checkContentListExists($dataId);
    }

    /**
     * Check if the wanted content list exists
     *
     * @param int $contentListId
     * @return boolean
     */
    protected function checkContentListExists($contentListId)
    {
        $contentList = $this->_contentListCollectionFactory->create()->addStoreFilter($this->_storeManager->getStore()
            ->getId())->addFieldToFilter('main_table.' . ContentListModel::ID, $contentListId);

        // Load the content list according to the preview mode
        if ($this->getPreviewParam() != 1) {
            $contentList->addFieldToFilter('main_table.' . ContentListModel::STATUS, 1);
        }

        return ($contentList->getSize() > 0);
    }
}
