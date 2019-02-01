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

use Blackbird\ContentManager\Model\Content as ContentModel;
use Blackbird\ContentManager\Model\ContentType as ContentTypeModel;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml
 */
abstract class Content extends \Magento\Backend\App\Action
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
     * @var \Blackbird\ContentManager\Model\Content
     */
    protected $_contentModel;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    protected $resultRedirect;

    /**
     * Content constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_datetime = $datetime;
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_modelFactory = $modelFactory;
        $this->resultRedirect = $this->resultRedirectFactory->create();
    }

    /**
     * Initiate action
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initAction()
    {
        // Load current content
        $this->_loadContent();

        // Load current content type
        $this->_loadContentType();

        return $this;
    }

    /**
     * Load Current Content
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _loadContent()
    {
        $contentId = $this->getRequest()->getParam('id');

        if (!empty($contentId)) {
            $contentCollection = $this->_contentCollectionFactory->create()
                ->addStoreFilter($this->getRequest()->getParam('store', 0))
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(\Blackbird\ContentManager\Model\Content::ID, $contentId);

            if ($contentCollection->count()) {
                $this->_contentModel = $contentCollection->getFirstItem();
                $this->_coreRegistry->register('current_content', $this->_contentModel);
            } else {
                $this->messageManager->addErrorMessage(__('This content no longer exists.'));
                $this->resultRedirect->setPath('*/*/', ['ct_id' => $this->_getCtId()]);
            }
        }

        return $this;
    }

    /**
     * Retrieve the related content type id
     *
     * @return int|null
     */
    protected function _getCtId()
    {
        return ($this->_contentModel) ? $this->_contentModel->getCtId() : $this->getRequest()->getParam('ct_id');
    }

    /**
     * Load relative Content Type
     *
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function _loadContentType()
    {
        $contentTypeId = $this->_getCtId();

        if (is_numeric($contentTypeId)) {
            $contentTypeCollection = $this->_contentTypeCollectionFactory->create()
                ->addFieldToFilter(ContentTypeModel::ID, $contentTypeId);

            if ($contentTypeCollection->count()) {
                $this->_contentTypeModel = $contentTypeCollection->getFirstItem();
            } else {
                $this->messageManager->addErrorMessage(__('This content type no longer exists.'));

                return $this->resultRedirect->setPath('*/contenttype/');
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
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contents');
    }
}
