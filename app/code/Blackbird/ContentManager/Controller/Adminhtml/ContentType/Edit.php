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

namespace Blackbird\ContentManager\Controller\Adminhtml\ContentType;

use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Edit
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentType
 */
class Edit extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
{
    /**
     * Edit content type action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->_initAction();

        if (!$this->_contentTypeModel && !empty($pageData)) {
            $this->_contentTypeModel = $this->_modelFactory->create(ContentType::class);
            $this->_contentTypeModel->addData($pageData);
        }

        if ($this->_contentTypeModel) {
            $this->_addBreadcrumb(__('Edit Content Type \'%1\'', $this->_contentTypeModel->getTitle()),
                __('Edit Content Type \'%1\'', $this->_contentTypeModel->getTitle()));
            $this->_view->getPage()->getConfig()->getTitle()->prepend($this->_contentTypeModel->getTitle());
        } else {
            $this->_addBreadcrumb(__('New Content Type'), __('New Content Type'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Content Type'));
        }

        $this->_view->renderLayout();
    }
}
