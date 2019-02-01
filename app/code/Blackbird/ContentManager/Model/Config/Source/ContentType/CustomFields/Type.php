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

namespace Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields;

use Magento\Framework\Config\Data;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
 */
class Type implements ArrayInterface
{
    /**
     * @var \Magento\Framework\Config\Data
     */
    private $_customFieldConfig;

    /**
     * @param \Magento\Framework\Config\Data $config
     */
    public function __construct(Data $config)
    {
        $this->_customFieldConfig = $config;
    }

    /**
     * Get custom fields renderer
     *
     * @param bool $allowRepeaterType [optional] Allows repeater field types flag.
     * @return array
     */
    public function getCustomFieldsRenderer($allowRepeaterType = true)
    {
        $fields = [];
        foreach ($this->getCustomFieldTypeSource($allowRepeaterType) as $field) {
            $fields[] = ['name' => $field['name'], 'renderer' => $field['renderer']];
        }

        return $fields;
    }

    /**
     * Retrieve all content type source
     *
     * @param bool $allowRepeaterType [optional] Allows repeater field types flag.
     * @return array
     */
    public function getCustomFieldTypeSource($allowRepeaterType = true)
    {
        if ($allowRepeaterType) {
            return $this->_customFieldConfig->get();
        }

        $customFieldSource = [];

        foreach ($this->_customFieldConfig->get() as $customField) {
            foreach ($customField['types'] as $id => $input) {
                if (!$input['is_repeater_compatible']) {
                    unset($customField['types'][$id]);
                }
            }
            if (count($customField['types'])) {
                $customFieldSource[] = $customField;
            }
        }

        return $customFieldSource;
    }

    /**
     * Get custom fields types renderer
     *
     * @param bool $allowRepeaterType [optional] Allows repeater field types flag.
     * @return array
     */
    public function getCustomFieldsTypesRenderer($allowRepeaterType = true)
    {
        $fields = [];

        foreach ($this->getCustomFieldTypeSource($allowRepeaterType) as $field) {
            foreach ($field['types'] as $fieldtype) {
                $fields[$fieldtype['name']] = $fieldtype['renderer'];
            }
        }

        return $fields;
    }

    /**
     * Get custom fields option renderer
     *
     * @param bool $allowRepeaterType [optional] Allows repeater field types flag.
     * @return array
     */
    public function getCustomFieldsOptionRenderer($allowRepeaterType = true)
    {
        $fields = [];
        foreach ($this->getCustomFieldTypeSource($allowRepeaterType) as $field) {
            if (!empty($field['option_renderer'])) {
                $fields[] = ['name' => $field['name'], 'renderer' => $field['option_renderer']];
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     * @param bool $allowRepeaterType [optional] Allows repeater field types flag.
     * @return array
     */
    public function toOptionArray($allowRepeaterType = true)
    {
        $groups = [['value' => '', 'label' => __('-- Please select --')]];

        foreach ($this->getCustomFieldTypeSource($allowRepeaterType) as $field) {
            $types = [];
            foreach ($field['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                $types[] = ['label' => __($type['label']), 'value' => $type['name']];
            }
            if (count($types)) {
                $groups[] = ['label' => __($field['label']), 'value' => $types, 'optgroup-name' => $field['name']];
            }
        }

        return $groups;
    }

    /**
     * Get Select Types
     *
     * @return array
     */
    public function getSelectTypes()
    {
        $types = [
            'currency',
            'locale',
        ];

        foreach ($this->_customFieldConfig->get() as $field) {
            if ($field['name'] === 'select') {
                foreach ($field['types'] as $type) {
                    $types[] = $type['name'];
                }
            }
        }

        return $types;
    }

    /**
     * Return frontend renderer type corresponding to contenttype type
     * For render in FORM (when creating new content)
     *
     * @param string $fieldType
     * @return string
     */
    public function getRendererTypeByFieldType($fieldType)
    {
        $fieldTypeToDataType = [
            'field' => 'text',
            'area' => 'textarea',
            'editor' => 'editor',
            'password' => 'password',
            'file' => 'file',
            'image' => 'image',
            'drop_down' => 'select',
            'radio' => 'radios',
            'checkbox' => 'checkboxes',
            'multiple' => 'multiselect',
            'date' => 'date',
            'date_time' => 'date',
            'time' => 'time',
            'int' => 'text',
            'country' => 'select',
            'currency' => 'select',
            'locale' => 'select',
            // Special type for category field
            'category' => '\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category',
        ];

        return isset($fieldTypeToDataType[$fieldType]) ? $fieldTypeToDataType[$fieldType] : 'text';
    }
}
