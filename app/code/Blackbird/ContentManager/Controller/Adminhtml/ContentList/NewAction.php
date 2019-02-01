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
 * Class NewAction
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class NewAction extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
{
    /**
     * New content list action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
