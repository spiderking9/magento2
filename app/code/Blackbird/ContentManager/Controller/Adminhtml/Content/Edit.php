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

namespace Blackbird\ContentManager\Controller\Adminhtml\Content;

use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Edit
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class Edit extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initAction();

        // Check if contenttype exists
        if (!$this->_contentTypeModel instanceof ContentType) {
            // Redirect to the content type main page
            $this->messageManager->addErrorMessage(__('Create a new content type, or select one in the content manager menu.'));

            return $this->resultRedirect->setPath('*/contenttype/');
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::content');

        if ($this->_contentModel) {
            $this->_addBreadcrumb(__('Edit Content \'%1\'', $this->_contentTypeModel->getTitle()),
                __('Edit Content \'%1\' \'%2\'', $this->_contentTypeModel->getTitle(),
                    $this->_contentModel->getTitle()));
            $this->_view->getPage()->getConfig()->getTitle()->prepend($this->_contentModel->getTitle());
        } else {
            $this->_addBreadcrumb(__('New Content \'%1\'', $this->_contentTypeModel->getTitle()),
                __('New Content \'%1\'', $this->_contentTypeModel->getTitle()));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New \'%1\'',
                $this->_contentTypeModel->getTitle()));
        }

        $this->_view->renderLayout();
    }
}
