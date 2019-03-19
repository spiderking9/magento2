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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget\ContentList;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class AttributeShow
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\ContentList
 */
class AttributeShow extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel
     */
    protected $_labelOptions;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\Content\Widget\ContentList\AttributeShow
     */
    protected $_attributeShowConfig;
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $_customFields;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/widget/contentlist/attributes-show.phtml';

    /**
     * AttributeShow constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\Content\Widget\ContentList\AttributeShow $attributeShowConfig
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFields
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\Content\Widget\ContentList\AttributeShow $attributeShowConfig,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFields,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_attributeShowConfig = $attributeShowConfig;
        $this->_customFields = $customFields;
        $this->_coreRegistry = $registry;
        $this->_labelOptions = $labelOptions;

        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $this->setElement($element);
        $element->setData('after_element_html', $this->toHtml());

        return $element;
    }

    /**
     * Render the add attribute show button child element
     *
     * @return string
     */
    public function getAddAttributeShowButtonHtml()
    {
        return $this->getChildHtml('add_attribute_to_show');
    }

    /**
     * Returns all fields to adding an attribute to show
     *
     * @return string
     */
    public function getAttributeShowFieldsHtml()
    {
        $html = '';

        foreach ($this->_attributeShowConfig->toOptionArray() as $field) {
            $html .= $this->getChildHtml('field_' . $field['value']);
        }

        return $html;
    }

    /**
     * Returns a specific field
     *
     * @param string $name
     * @return string
     */
    public function getAttributeShowFieldHtml($name)
    {
        return $this->getChildHtml($name);
    }

    /**
     * Get the widget field encoded name
     *
     * @return string
     */
    public function getWidgetFieldEncodedName()
    {
        return 'parameters[attributes_show]';
    }

    /**
     * Get custom fields array
     *
     * @return array
     */
    public function getCustomFields()
    {
        $return = $this->_customFields->toArray();

        return $return;
    }

    /**
     * Return select 'how to show the label'
     *
     * @param string $id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelSelectHtml($id = '')
    {
        /** @var \Magento\Framework\View\Element\Html\Select $select */
        $select = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Html\Select::class)->setData([
            'id' => $id,
            'class' => 'select select-contenttype-layout-block-label required-entry',
            'label' => __('Label Option'),
            'title' => __('Label Option'),
        ])->setName($this->getWidgetFieldName('label'))->setOptions($this->_labelOptions->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Get the widget field code by name
     *
     * @param $name
     * @return string
     */
    public function getWidgetFieldName($name)
    {
        return '<%- data.prefix %>parameters[attributes_show][<%- data.id %>][' . $name . ']';
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $selects = ['select', 'multiselect', 'radios', 'checkboxes'];

        // Prepare add attribute to show button
        $this->addChild('add_attribute_to_show', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Add Attribute to Show'),
            'class' => 'add',
            'id' => 'add_attribute_to_show',
        ]);

        // Prepare fields to add attribute to show
        foreach ($this->_attributeShowConfig->toOptionArray() as $field) {
            $name = $this->getWidgetFieldName($field['value']);
            $id = $this->getWidgetFieldId() . '-' . $field['value'];
            $input = 'Magento\Framework\View\Element\Text';

            $data = [
                'name' => $name,
                'data' => [
                    'id' => $id,
                    'class' => '',
                    'label' => $field['label'],
                    'title' => $field['label'],
                ],
            ];

            // If type of select, add the source options
            if (in_array($field['input'], $selects) && !empty($field['source'])) {
                $data['options'] = $field['source'];
                $input = 'Magento\Framework\View\Element\Html\Select';
            }

            $this->addChild('field_' . $field['value'], $input, $data);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get the widget field id
     *
     * @return string
     */
    public function getWidgetFieldId()
    {
        return 'attribute-show-<%- data.id %>';
    }
}
