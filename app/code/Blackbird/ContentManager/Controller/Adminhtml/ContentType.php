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

/**
 * Class ContentType
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml
 */
abstract class ContentType extends \Magento\Backend\App\Action
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
     * @var \Blackbird\ContentManager\Model\ContentType
     */
    protected $_contentTypeModel;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_contentTypeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $_cacheManager;

    /**
     * ContentType constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_datetime = $datetime;
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->_modelFactory = $modelFactory;
        $this->resultRedirect = $this->resultRedirectFactory->create();
        $this->_cacheManager = $cacheManager;
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::content')->_addBreadcrumb(__('Content Manager'), __('Content Manager'));

        $this->_loadContentType();

        return $this;
    }

    /**
     * Load content type
     *
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function _loadContentType()
    {
        // Get contentTypeId param if provided
        $contentTypeId = $this->getRequest()->getParam('id');

        if (is_numeric($contentTypeId)) {
            $contentTypeCollection = $this->_contentTypeCollectionFactory->create()
                ->addFieldToFilter('ct_id', $contentTypeId);

            if ($contentTypeCollection->count()) {
                $this->_contentTypeModel = $contentTypeCollection->getFirstItem();
            } else {
                $this->messageManager->addErrorMessage(__('This content type no longer exists.'));

                return $this->resultRedirect->setPath('*/*/');
            }
        }

        if ($this->_contentTypeModel) {
            $this->_coreRegistry->register('current_contenttype', $this->_contentTypeModel);
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
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contenttype');
    }

    /**
     * Flushes backend main menu cache storages
     */
    protected function flushBackendMainMenuCache()
    {
        $this->_cacheManager->flush([\Magento\Backend\Block\Menu::CACHE_TAGS]);
    }
}
