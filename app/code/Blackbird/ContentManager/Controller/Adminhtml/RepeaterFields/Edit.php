<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package        Blackbird_ContentManager
 * @copyright           Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author        Blackbird Team
 * @license        http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Controller\Adminhtml\RepeaterFields;

use Blackbird\ContentManager\Model\ContentType;

class Edit extends \Blackbird\ContentManager\Controller\Adminhtml\RepeaterFields
{
    /**
     * Edit content type action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();

        $pageData = $this->_getSession()->getPageData(true);
        if ($this->_contentTypeModel && !empty($pageData)) {
            $this->_contentTypeModel = $this->_modelFactory->create(ContentType::class);
            $this->_contentTypeModel->addData($pageData);
        }

        if ($this->_contentTypeModel) {
            $this->_addBreadcrumb(__('Edit Repeater Fields \'%1\'', $this->_contentTypeModel->getTitle()),
                __('Edit Repeater Fields \'%1\'', $this->_contentTypeModel->getTitle()));
            $this->_view->getPage()->getConfig()->getTitle()->prepend($this->_contentTypeModel->getTitle());
        } else {
            $this->_addBreadcrumb(__('New Repeater Fields'), __('New Repeater Fields'));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Repeater Fields'));
        }

        $this->_view->renderLayout();
    }
}
