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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout\Templates\Item;

/**
 * Class Field
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout\Templates\Item
 */
class Field extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Field
{
    /**
     * Get custom fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        $contentList = $this->_coreRegistry->registry('current_contentlist');
        $contentType = $contentList ? $contentList->getContentType() : null;
        $return = $contentType ? $this->_customFields->toArray($contentType->getCtId()) : [];

        return $return;
    }
}
