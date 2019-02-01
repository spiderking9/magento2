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

namespace Blackbird\ContentManager\Block;

/**
 * Class View
 *
 * @package Blackbird\ContentManager\Block
 */
class View extends \Blackbird\ContentManager\Block\Content\Widget\View
{
    /**
     * Get current content
     *
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContent()
    {
        if (!parent::getContent()) {
            $this->setData('content', $this->_coreRegistry->registry('current_content'));
        }

        return $this->getData('content');
    }
}
