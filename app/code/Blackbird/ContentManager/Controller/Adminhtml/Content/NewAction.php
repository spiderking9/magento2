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

/**
 * Class NewAction
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class NewAction extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    /**
     * New content action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->getRequest()->setParam('store', $this->getRequest()->getParam('store', 0));
        $this->_forward('edit');
    }
}
