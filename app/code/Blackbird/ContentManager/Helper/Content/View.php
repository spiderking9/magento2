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

namespace Blackbird\ContentManager\Helper\Content;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Content View helper
 *
 * @package Blackbird\ContentManager\Helper\Content
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
     * Content
     *
     * @var \Blackbird\ContentManager\Helper\Content
     */
    protected $_helperContent = null;

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
     * @param \Blackbird\ContentManager\Helper\Content $helperContent
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Blackbird\ContentManager\Helper\Content $helperContent,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helperContent = $helperContent;
        $this->_coreRegistry = $coreRegistry;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Prepares content view page - inits layout and all needed stuff
     *
     * @param ResultPage $resultPage
     * @param int $contentId
     * @param \Magento\Framework\App\Action\Action $controller
     * @return ResultPage
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareAndRender(ResultPage $resultPage, $contentId, Action $controller)
    {
        // Standard algorithm to prepare and render product view page
        $content = $this->_helperContent->initContent($contentId, $controller);
        if (!$content) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Content is not loaded'));
        }

        $this->_eventManager->dispatch('contentmanager_controller_content_view', ['content' => $content]);

        $resultPage = $this->initContentLayout($resultPage, $content);
        if (!$controller instanceof \Blackbird\ContentManager\Controller\Index\View\ViewInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Bad controller interface for showing content'));
        }

        return $resultPage;
    }

    /**
     * Init layout for viewing content page
     *
     * @param ResultPage $resultPage
     * @param Content $content
     * @return ResultPage
     */
    public function initContentLayout(ResultPage $resultPage, Content $content)
    {
        // Load the contenttype
        $contentType = $content->getContentType();

        // Content type dynamic layout handler
        $pageConfig = $resultPage->getConfig();

        // Root template
        if ($contentType->getRootTemplate()) {
            $pageConfig->setPageLayout($contentType->getRootTemplate());
        }

        // Load the layout
        $resultPage->getLayout()->getUpdate()->addHandle('CONTENT_TYPE_VIEW_' . $contentType->getIdentifier());
        $resultPage->getLayout()->getUpdate()->addHandle('CONTENT_VIEW_' . str_replace('/', '_', $content->getUrlKey()));
        $layoutUpdate = $contentType->getLayoutUpdateXml();
        if ($layoutUpdate) {
            $resultPage->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }

        // Add the body class
        $controllerClass = $this->_request->getFullActionName();
        if ($controllerClass != 'contentmanager-content-view') {
            $pageConfig->addBodyClass('contentmanager-content-view');
        }
        $pageConfig->addBodyClass('contentmanager-contenttype-' . $contentType->getIdentifier());
        $pageConfig->addBodyClass('contentmanager-content-' . $content->getId());

        // Apply the breadcrumbs
        $this->_applyBreadcrumbs($resultPage, $content, $contentType);

        // Apply the title and the meta tags
        $this->_applyTitleMeta($resultPage, $content);

        return $resultPage;
    }

    /**
     * Apply breadcrumbs based on content type configuration
     *
     * @param ResultPage $resultPage
     * @param Content $content
     * @param ContentType $contentType
     */
    protected function _applyBreadcrumbs(ResultPage $resultPage, Content $content, ContentType $contentType)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs && $contentType->getBreadcrumb()) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl(),
            ]);

            $storeId = $this->_storeManager->getStore()->getId();

            if ($contentType->getBreadcrumbPrevName()) {
                $breadcrumbPrevName = unserialize($contentType->getBreadcrumbPrevName());
                $breadcrumbPrevLink = $contentType->getBreadcrumbPrevLink()
                    ? unserialize($contentType->getBreadcrumbPrevLink()) : [];

                if (!empty($breadcrumbPrevName[$storeId])) {
                    $crumbNames = explode(';', $this->_applyPattern($content, $breadcrumbPrevName[$storeId]));
                    $crumbLinks = isset($breadcrumbPrevLink[$storeId]) ? explode(';',
                        $this->_applyPattern($content, $breadcrumbPrevLink[$storeId])) : [];

                    foreach ($crumbNames as $key => $crumbName) {
                        $crumbLink = isset($crumbLinks[$key]) ? $this->_getUrl('',
                            ['_direct' => trim($crumbLinks[$key], " /\t\n\r\0\x0B")]) : '';

                        $breadcrumbs->addCrumb('cct_content_prev_' . $key, [
                            'label' => $crumbName,
                            'title' => $crumbName,
                            'link' => $crumbLink,
                        ]);
                    }
                }
            }

            if ($contentType->getBreadcrumb()) {
                $breadcrumbs->addCrumb('cct_content', [
                    'label' => $content->getAttributeText($contentType->getBreadcrumb()),
                    'title' => $content->getAttributeText($contentType->getBreadcrumb()),
                ]);
            }
        }
    }

    /**
     * Aplly pattern to content
     *
     * @param Content $content
     * @param string $value
     * @return string
     */
    protected function _applyPattern(Content $content, $value)
    {
        $matches = [];
        preg_match_all('/{{([a-zA-Z0-9_\|]*)}}/', (string)$value, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $key => $replacement) {
                $attributeContent = $content->getData($replacement);

                if (preg_match('/\|plain/', $replacement)) {
                    $replacement = str_replace('|plain', '', $replacement);
                    $attributeContent = strip_tags($content->getData($replacement));

                }

                $value = str_replace($matches[0][$key], $attributeContent, $value);
            }
        }

        return $value;
    }

    /**
     * Apply meta tags based on content type configuration
     *
     * @param ResultPage $resultPage
     * @param Content $content
     */
    protected function _applyTitleMeta(ResultPage $resultPage, Content $content)
    {
        $pageConfig = $resultPage->getConfig();

        // Add the title and meta tags
        $pageConfig->getTitle()->set($content->getMetaTitle() ? $content->getMetaTitle() : $content->getTitle());
        $pageConfig->setKeywords($content->getKeywords());
        $pageConfig->setDescription($content->getDescription());
        $pageConfig->setRobots($content->getRobots());

        // Add the open graph block
        if ($content->getOgTitle()) {
            $pageConfig->setMetadata('og:title', $content->getOgTitle());
        }
        if ($content->getOgDescription()) {
            $pageConfig->setMetadata('og:description', $content->getOgDescription());
        }
        if ($content->getOgUrl()) {
            $pageConfig->setMetadata('og:url', $content->getOgUrl());
        }
        if ($content->getOgType()) {
            $pageConfig->setMetadata('og:type', $content->getOgType());
        }
        if ($content->getOgImage()) {
            $pageConfig->setMetadata('og:image', $content->getOgImage());
        }

        // Add Canonical Tag
        //todo force to true
        if (true || $content->getMetaCanonical()) {
            $pageConfig->addRemotePageAsset($content->getLinkUrl(), 'canonical',
                ['attributes' => ['rel' => 'canonical']]);
        }
    }
}
