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

namespace Blackbird\ContentManager\Controller\Adminhtml;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml
 */
abstract class ContentList extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_datetime;

    /**
     * @var \Blackbird\ContentManager\Model\ContentList
     */
    protected $_contentListModel;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    protected $resultRedirect;

    /**
     * ContentList constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_datetime = $datetime;
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
        $this->_modelFactory = $modelFactory;
        $this->resultRedirect = $this->resultRedirectFactory->create();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        // Load current content list
        $this->_loadContentList();

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::content')->_addBreadcrumb(__('Content Manager'), __('Content Manager'));

        return $this;
    }

    /**
     * Load content list
     *
     * @return $this
     */
    protected function _loadContentList()
    {
        // Get contentTypeId param if provided
        $contentListId = $this->getRequest()->getParam('id');

        if (is_numeric($contentListId)) {
            $contentListCollection = $this->_contentListCollectionFactory->create()
                ->addFieldToFilter(ContentListData::ID, $contentListId);

            if ($contentListCollection->count()) {
                $this->_contentListModel = $contentListCollection->getFirstItem();
            } else {
                $this->messageManager->addErrorMessage(__('This content list no longer exists.'));

                return $this->resultRedirect->setPath('*/*/');
            }
        }

        if ($this->_contentListModel) {
            $this->_coreRegistry->register('current_contentlist', $this->_contentListModel);
        }

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contentlist');
    }
}
