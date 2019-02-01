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

namespace Blackbird\ContentManager\Controller\Adminhtml\ContentList;

/**
 * Class Index
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class Index extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
{
    /**
     * ContentList Index Action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_addBreadcrumb(__('Content Lists'), __('Content Lists'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Content Lists'));
        $this->_view->renderLayout();
    }
}
