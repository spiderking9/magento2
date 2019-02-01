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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields;

class Field extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/fields/field.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type
     */
    protected $_fieldType;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\Weight
     */
    protected $_searchWeightSource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldType
     * @param \Blackbird\ContentManager\Model\Config\Source\Weight $searchAttributeWrightSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldType,
        \Blackbird\ContentManager\Model\Config\Source\Weight $searchAttributeWrightSource,
        array $data = []
    ) {
        $this->_fieldType = $fieldType;
        $this->_searchWeightSource = $searchAttributeWrightSource;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve html templates for different types of content type custom fields
     *
     * @return string
     */
    public function getTemplatesFieldsHtml()
    {
        $templates = '';

        foreach ($this->_fieldType->getCustomFieldsRenderer() as $type) {
            $templates .= $this->getChildHtml($type['name']) . PHP_EOL;
        }

        return $templates;
    }

    /**
     * Retrieve search weights as select field
     *
     * @return type
     */
    public function getSearchWeightSelectHtml()
    {
        $select = $this->getLayout()
            ->createBlock(\Magento\Framework\View\Element\Html\Select::class)
            ->setData([
                'id' => $this->getFieldsetId() . '_<%- data.id %>_' . $this->getFieldId() . '_<%- data.field_id %>_search_weight',
                'class' => 'select select-contenttype-field-type required-custom-field-select required-entry',
            ])
            ->setName($this->getFieldsetName() . '[<%- data.id %>]' . $this->getFieldName() . '[<%- data.field_id %>][search_weight]')
            ->setOptions($this->_searchWeightSource->toOptionArray());

        return $select->getHtml();
    }

    /**
     * @return string
     */
    public function getFieldsetId()
    {
        return 'contenttype_fieldset';
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        return 'field';
    }

    /**
     * @return string
     */
    public function getFieldsetName()
    {
        return 'contenttype[fieldsets]';
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return '[fields]';
    }

    /**
     * Create a field type options select field
     *
     * @return string
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Html\Select')
            ->setData([
                'id' => $this->getFieldsetId() . '_<%- data.id %>_' . $this->getFieldId() . '_<%- data.field_id %>_type',
                'class' => 'select select-contenttype-field-type required-custom-field-select required-entry',
            ])
            ->setName($this->getFieldsetName() . '[<%- data.id %>]' . $this->getFieldName() . '[<%- data.field_id %>][type]')
            ->setOptions($this->_fieldType->toOptionArray($this->isRepeaterCompatible()));

        return $select->getHtml();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        foreach ($this->_fieldType->getCustomFieldsRenderer($this->isRepeaterCompatible()) as $type) {
            $this->addChild($type['name'], $type['renderer']);
        }

        return parent::_prepareLayout();
    }

    /**
     * If the current contenttype is already a repeater field, we prevent the
     * custom fields type of repeater fields
     *
     * @return bool
     */
    public function isRepeaterCompatible()
    {
        return !$this->getRequest()->getParam('is_repeater');
    }
}