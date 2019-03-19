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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout;

/**
 * Class Items
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout
 */
class Items extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Items
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        // Item Block Template
        $this->addChild('item_block_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Block::class);

        // Item Field Template
        $this->addChild('item_field_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout\Templates\Item\Field::class);

        // Item Group Template
        $this->addChild('item_group_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Group::class);

        return $this;
    }
}
