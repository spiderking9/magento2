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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout;

/**
 * Class Items
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout
 */
class Items extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/layout/items.phtml';

    /**
     * Render all items templates
     *
     * @return string
     */
    public function getItemsTemplatesHtml()
    {
        $templates = '';

        $templates .= $this->getItemBlockTemplateHtml();
        $templates .= $this->getItemFieldTemplateHtml();
        $templates .= $this->getItemGroupTemplateHtml();

        return $templates;
    }

    /**
     * Render block item template
     *
     * @return string
     */
    public function getItemBlockTemplateHtml()
    {
        $template = $this->getChildHtml('item_block_template');

        return $template;
    }

    /**
     * Render field item template
     *
     * @return string
     */
    public function getItemFieldTemplateHtml()
    {
        $template = $this->getChildHtml('item_field_template');

        return $template;
    }

    /**
     * Render group item  template
     *
     * @return string
     */
    public function getItemGroupTemplateHtml()
    {
        $template = $this->getChildHtml('item_group_template');

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // Item Block Template
        $this->addChild('item_block_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Block::class);

        // Item Field Template
        $this->addChild('item_field_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Field::class);

        // Item Group Template
        $this->addChild('item_group_template',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item\Group::class);

        return $this;
    }
}
