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

namespace Blackbird\ContentManager\Block\Adminhtml\RepeaterFields\Edit;

/**
 * Class Tabs
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\RepeaterFields\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('contentmanager_contenttype_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Repeater Fields'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /**
         * Add tab Manage Fields
         * load custom_fields by ajax
         */
        $this->addTabAfter('custom_fields', [
            'label' => __('Manage Fields'),
            'url' => $this->getUrl('*/contenttype/fields', ['_current' => true, 'is_repeater' => true]),
            'class' => 'ajax',
        ], 'main_section');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->setActiveTab('main_section');

        parent::_beforeToHtml();
    }
}
