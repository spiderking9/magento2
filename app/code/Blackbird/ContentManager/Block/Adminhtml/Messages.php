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

namespace Blackbird\ContentManager\Block\Adminhtml;

/**
 * Class Messages
 *
 * @package Blackbird\ContentManager\Block\Adminhtml
 */
class Messages extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'messages.phtml';

    /**
     * Retrieve the messages info
     *
     * @return array
     */
    public function getMessages()
    {
        if (!$this->hasData('messages')) {
            $this->setData('messages', []);
        }

        return $this->getData('messages');
    }
}
