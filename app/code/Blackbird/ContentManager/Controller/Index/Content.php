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

use Blackbird\ContentManager\Model\Content as ContentModel;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Controller\Index
 */
class Content extends \Blackbird\ContentManager\Controller\Index\View
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * Content constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Blackbird\ContentManager\Helper\Content\View $helperView
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param string $paramName
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blackbird\ContentManager\Helper\Content\View $helperView,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        $paramName = 'content_id'
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $storeManager, $helperView, $resultForwardFactory, $resultPageFactory,
            $paramName);
    }

    /**
     * Check if the object exists
     *
     * @param int $dataId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isObjectExists($dataId)
    {
        return $this->checkContentExists($dataId);
    }

    /**
     * Check if the wanted content exists
     *
     * @param int $entityId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkContentExists($entityId)
    {
        $content = $this->_contentCollectionFactory->create()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addAttributeToFilter(ContentModel::ID, $entityId)
            ->addIsVisibleFilter(false);

        // Load the content according to the preview mode
        if ($this->getPreviewParam() != 1) {
            $content->addAttributeToFilter(ContentModel::STATUS, 1);
        }

        return (bool)$content->getSize();
    }
}
