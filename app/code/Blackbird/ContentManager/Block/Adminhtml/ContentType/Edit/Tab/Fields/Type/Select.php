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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type;

/**
 * Class Select
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type
 */
class Select extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type\AbstractType
{
    /**
     * Default template
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/fields/type/select.phtml';

    /**
     * Get add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    /**
     * Get delete button html
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_select_row_button', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Add New Row'),
            'class' => 'add add-select-row',
            'id' => 'contenttype_fieldset_<%- data.id %>_field_<%- data.field_id %>_add_select_row',
        ]);

        $this->addChild('delete_select_row_button', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Delete Row'),
            'class' => 'action-delete delete delete-select-row',
            'id' => 'contenttype_fieldset_<%- data.id %>_field_<%- data.field_id %>_select_<%- data.select_id %>_delete',
        ]);

        return parent::_prepareLayout();
    }


}
