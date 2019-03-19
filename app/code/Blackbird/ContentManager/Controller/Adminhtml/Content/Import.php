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

use Blackbird\ContentManager\Controller\Adminhtml\Content;

class Import extends Content
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initAction();

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::content');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Import Content'));
        $this->_addBreadcrumb(__('Import Content'), __('Import Content'));
        $this->_view->renderLayout();
    }
}
