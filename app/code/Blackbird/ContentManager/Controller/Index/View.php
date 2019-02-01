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

use Blackbird\ContentManager\Controller\Index\View\ViewInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @package Blackbird\ContentManager\Controller\Index
 */
abstract class View extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /**
     * @var string
     */
    protected $_paramName;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Helper\AbstractHelper
     */
    protected $_helperView;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\AbstractHelper $helperView
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param string $paramName
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\AbstractHelper $helperView,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        $paramName = ''
    ) {
        $this->_storeManager = $storeManager;
        $this->_helperView = $helperView;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_paramName = $paramName;
        parent::__construct($context);
    }

    /**
     * View Content page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        // Get initial data from request
        $dataId = (int)$this->getRequest()->getParam($this->_paramName);

        if (!$this->isObjectExists($dataId)) {
            return $resultForward->forward('noroute');
        }

        // Render page
        try {
            $resultPage = $this->resultPageFactory->create();
            $resultPage = $this->_helperView->prepareAndRender($resultPage, $dataId, $this);

            return $resultPage;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->noContentRedirect();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());

            return $resultForward->forward('noroute');
        }
    }

    /**
     * Check if the object exists (to override)
     *
     * @param int $dataId
     * @return boolean
     */
    abstract protected function isObjectExists($dataId);

    /**
     * Redirect if content failed to load
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Forward
     */
    protected function noContentRedirect()
    {
        $store = $this->getRequest()->getQuery('store');

        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('');
        } elseif (!$this->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }
    }

    /**
     * Retrieve the value of the 'preview' param name
     *
     * @return string
     */
    protected function getPreviewParam()
    {
        return $this->getRequest()->getParam('preview');
    }
}
