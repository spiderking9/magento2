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

namespace Blackbird\ContentManager\Helper\ContentList;

use Blackbird\ContentManager\Model\ContentList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Content View helper
 *
 * @package Blackbird\ContentManager\Helper\ContentList
 */
class View extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Content List
     *
     * @var \Blackbird\ContentManager\Helper\ContentList
     */
    protected $_helperContentList = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * View constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Blackbird\ContentManager\Helper\ContentList $helperContentList
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Blackbird\ContentManager\Helper\ContentList $helperContentList,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helperContentList = $helperContentList;
        $this->_coreRegistry = $coreRegistry;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Prepares content list view page - inits layout and all needed stuff
     *
     * @param ResultPage $resultPage
     * @param int $contentListId
     * @param Action $controller
     * @return ResultPage
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareAndRender(ResultPage $resultPage, $contentListId, Action $controller)
    {
        // Standard algorithm to prepare and render content view page
        $contentList = $this->_helperContentList->initContentList($contentListId, $controller);
        if (!$contentList) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Content List is not loaded'));
        }

        $this->_eventManager->dispatch('contentmanager_controller_contentlist_view', ['contentlist' => $contentList]);

        $resultPage = $this->initContentListLayout($resultPage, $contentList);
        if (!$controller instanceof \Blackbird\ContentManager\Controller\Index\View\ViewInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Bad controller interface for showing content list'));
        }

        return $resultPage;
    }

    /**
     * Init layout for viewing content list page
     *
     * @param ResultPage $resultPage
     * @param ContentList $contentList
     * @return ResultPage
     */
    public function initContentListLayout(ResultPage $resultPage, ContentList $contentList)
    {
        // Content type dynamic layout handler
        $pageConfig = $resultPage->getConfig();

        // Root template
        if ($contentList->getRootTemplate()) {
            $pageConfig->setPageLayout($contentList->getRootTemplate());
        }

        // Load the layout
        $resultPage->getLayout()->getUpdate()->addHandle($this->retrieveContentListIdentifier($contentList));
        $layoutUpdate = $contentList->getLayoutUpdateXml();
        if ($layoutUpdate) {
            $resultPage->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }

        // Add the body class
        $controllerClass = $this->_request->getFullActionName();
        if ($controllerClass != 'contentmanager-contentlist-view') {
            $pageConfig->addBodyClass('contentmanager-contentlist-view');
        }
        $pageConfig->addBodyClass('contentmanager-contentlist-' . $contentList->getId());

        // Apply the breadcrumbs
        $this->_applyBreadcrumbs($resultPage, $contentList);

        // Apply the title and the meta tags
        $this->_applyTitleMeta($resultPage, $contentList);

        return $resultPage;
    }

    /**
     * Retrieve the ContentList identifier
     *
     * @param \Blackbird\ContentManager\Model\ContentList $contentList
     * @return string
     */
    protected function retrieveContentListIdentifier(ContentList $contentList)
    {
        return 'CONTENT_LIST_VIEW_' . str_replace('/', '_', $contentList->getUrlKey());
    }

    /**
     * Apply breadcrumbs based on content list configuration
     *
     * @param ResultPage $resultPage
     * @param ContentList $contentList
     */
    protected function _applyBreadcrumbs(ResultPage $resultPage, ContentList $contentList)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs && $contentList->getBreadcrumb()) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl(),
            ]);

            $storeId = $this->_storeManager->getStore()->getId();

            if ($contentList->getBreadcrumbPrevName()) {
                $breadcrumbPrevName = unserialize($contentList->getBreadcrumbPrevName());
                $breadcrumbPrevLink = $contentList->getBreadcrumbPrevLink()
                    ? unserialize($contentList->getBreadcrumbPrevLink()) : [];

                if (!empty($breadcrumbPrevName[$storeId])) {
                    $crumbNames = explode(';', $breadcrumbPrevName[$storeId]);
                    $crumbLinks = isset($breadcrumbPrevLink[$storeId]) ? explode(';', $breadcrumbPrevLink[$storeId])
                        : [];

                    foreach ($crumbNames as $key => $crumbName) {
                        $breadcrumbs->addCrumb('cct_content_prev_' . $key, [
                            'label' => $crumbName,
                            'title' => $crumbName,
                            'link' => isset($crumbLinks[$key]) ? $this->_getUrl('',
                                ['_direct' => trim($crumbLinks[$key], " /\t\n\r\0\x0B")]) : '',
                        ]);
                    }
                }
            }

            if ($contentList->getBreadcrumb()) {
                $breadcrumbs->addCrumb('cct_content', [
                    'label' => $contentList->getData($contentList->getBreadcrumb()),
                    'title' => $contentList->getData($contentList->getBreadcrumb()),
                ]);
            }
        }
    }

    /**
     * Apply meta tags based on content type configuration
     *
     * @param ResultPage $resultPage
     * @param ContentList $contentList
     */
    protected function _applyTitleMeta(ResultPage $resultPage, ContentList $contentList)
    {
        $pageConfig = $resultPage->getConfig();

        // Add the title and meta tags
        $pageConfig->getTitle()->set($contentList->getMetaTitle() ? $contentList->getMetaTitle()
            : $contentList->getTitle());
        $pageConfig->setKeywords($contentList->getMetaKeywords());
        $pageConfig->setDescription($contentList->getMetaDescription());
        $pageConfig->setRobots($contentList->getMetaRobots());

        // Add the open graph block
        if ($contentList->getOgTitle()) {
            $pageConfig->setMetadata('og:title', $contentList->getOgTitle());
        }
        if ($contentList->getOgDescription()) {
            $pageConfig->setMetadata('og:description', $contentList->getOgDescription());
        }
        if ($contentList->getOgUrl()) {
            $pageConfig->setMetadata('og:url', $contentList->getOgUrl());
        }
        if ($contentList->getOgType()) {
            $pageConfig->setMetadata('og:type', $contentList->getOgType());
        }
        if ($contentList->getOgImage()) {
            $pageConfig->setMetadata('og:image', $contentList->getOgImage());
        }

        // Add Canonical Tag
        //todo force to true
        if (true || $contentList->getMetaCanonical()) {
            $pageConfig->addRemotePageAsset($contentList->getContentListUrl(), 'canonical',
                ['attributes' => ['rel' => 'canonical']]);
        }
    }
}
