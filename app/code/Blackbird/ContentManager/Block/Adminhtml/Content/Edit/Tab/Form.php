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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option\Collection as OptionCollection;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Class Form
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab
 */
class Form extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\AbstractTab
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type
     */
    protected $_fieldTypeSource;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enabledisable;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale
     */
    protected $_locale;

    /**
     * Form constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldTypeSource
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Config\Model\Config\Source\Locale\Currency $currency
     * @param \Magento\Config\Model\Config\Source\Locale $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldTypeSource,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Config\Model\Config\Source\Locale\Currency $currency,
        \Magento\Config\Model\Config\Source\Locale $locale,
        array $data = []
    ) {
        $this->_fieldTypeSource = $fieldTypeSource;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_enabledisable = $enabledisable;
        $this->_country = $country; // todo improve
        $this->_currency = $currency; // todo improve
        $this->_locale = $locale; // todo improve
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get the tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content Details');
    }

    /**
     * Get the tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Content Details');
    }

    /**
     * Show tab in adminhtml
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden or not
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('content_');

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $content = $this->_coreRegistry->registry('current_content');

        // General information fieldset
        $fieldset = $form->addFieldset('general_title', ['legend' => __('Content Details')]);

        // Title of the current content
        $fieldset->addField('title', 'text', [
            'name' => 'title',
            'label' => __('Page Title'),
            'title' => __('Page Title'),
            'required' => true,
            ($content && $content->getData(Content::DEFAULT_TITLE) == '1') ? 'readonly'
                : '' => ($content && $content->getData(Content::DEFAULT_TITLE) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_title',
                'id' => 'use_default_title',
                'label' => __('Use default Title'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_TITLE) : '0',
                'default' => $contentType->getData(ContentType::PAGE_TITLE),
                'value' => $content ? $content->getData(Content::TITLE) : '',
                'parent' => 'title',
            ]),
        ]);

        // Status of the current content
        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'required' => true,
            'values' => $this->_enabledisable->toOptionArray(),
        ]);

        // Store id of the current content
        $fieldset->addField('store_id', 'hidden', ['name' => 'store_id']);

        //input hidden containing ids of Repeater fields that will be removed on save action
        $fieldset->addField("repeater-field-repeaters-to-delete", 'hidden',
            ['name' => 'repeater-field[repeaters-to-delete]']);

        /**
         * Create the fieldsets and fields of the content type
         */

        // Default values
        $defaultValues = ['status' => $contentType->getDefaultStatus()];

        /** @var \Blackbird\ContentManager\Model\ContentType\CustomFieldset $customFieldset */
        foreach ($contentType->getCustomFieldsetCollection() as $customFieldset) {
            $fieldset = $form->addFieldset('fieldset_' . $customFieldset->getId(), [
                'legend' => $customFieldset->getTitle(),
                'collapsable' => true,
            ]);

            /** @var \Blackbird\ContentManager\Model\ContentType\CustomField $customField */
            foreach ($customFieldset->getCustomFieldCollection() as $customField) {
                //TODO : Warning customfield is updated by reference in this function
                $config = $this->getCustomFieldConfiguration($customField);

                // Create the field
                $fieldset->addField(
                    $customField->getIdentifier(),
                    $this->_fieldTypeSource->getRendererTypeByFieldType($customField->getType()),
                    $config
                );

                // Prepare the renderer if it exists
                $renderer = $this->getCustomFieldTypeRenderer($customField->getType());
                if (!empty($renderer)) {
                    $form->getElement($customField->getIdentifier())->setRenderer($this->getLayout()
                        ->createBlock($renderer, '', $this->_prepareDataBlock($customField, $content)));
                }

                // Add additional fields
                $this->addAdditionalFields($fieldset, $customField);

                // todo explore the issue: Special case for checboxes field, cast the data
                if ($content && $customField->getType() === 'checkbox') {
                    $content->setData($customField->getIdentifier(),
                        explode(',', $content->getData($customField->getIdentifier())));
                }

                // Default Values
                $defaultValues = array_merge($defaultValues,
                    $this->getCustomFieldDefaultValue($customField, $defaultValues));

                // End fields
            }
            // End fieldsets
        }

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_content_general_prepareform', ['form' => $form]);

        $data = [];
        if ($content) {
            $data = $content->getData();
        }

        $form->setValues(array_merge($defaultValues, $data));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Build the custom field configuration
     *
     * @param CustomField $customField
     * @return array
     */
    protected function getCustomFieldConfiguration(CustomField $customField)
    {
        $config = [
            'name' => $customField->getIdentifier(),
            'label' => $customField->getTitle(),
            'title' => $customField->getTitle(),
            'required' => $customField->getIsRequire(),
            'note' => $customField->getNote(),
        ];

        // Build the field configuration
        switch ($customField->getType()) {
            case 'area':
                if ($customField->getWysiwygEditor()) {
                    $customField->setType('editor');
                    $config['config'] = $this->_wysiwygConfig->getConfig(['add_directives' => true]);
                }
                $config = array_merge($config, $this->getMaxLengthConfig($customField->getMaxCharacters()));
                break;
            case 'date':
                $config = array_merge($config, $this->getDateConfig());
                break;
            case 'date_time':
                $config = array_merge($config, $this->getDatetimeConfig());
                break;
            case 'radio':
            case 'drop_down':
            case 'multiple':
                $config['values'] = $this->getValuesConfig($customField->getOptionCollection());
                break;
            case 'checkbox':
                $config['name'] = $config['name'] . '[]';
                $config['values'] = $this->getValuesConfig($customField->getOptionCollection());
                break;
            case 'image':
                unset($config['require']);
                break;
            case 'country':
                $config['values'] = $this->_country->toOptionArray();
                break;
            case 'currency':
                $config['values'] = $this->_currency->toOptionArray();
                break;
            case 'locale':
                $config['values'] = $this->_locale->toOptionArray();
                break;
            case 'file':
            case 'product':
            case 'category':
            case 'content':
            case 'attribute':
                break;
            // Text field by default
            default:
                if ($customField->getMaxCharacters()) {
                    $config = array_merge($config, $this->getMaxLengthConfig($customField->getMaxCharacters()));
                }
        }

        return $config;
    }

    /**
     * @param string $maxlength
     * @return array
     */
    protected function getMaxLengthConfig($maxlength = null)
    {
        $config = [];

        if (!empty($maxlength)) {
            $config['maxlength'] = $maxlength;
            if (!isset($config['class'])) {
                $config['class'] = '';
            }
            $config['class'] .= ' validate-length maximum-length-' . $maxlength;
        }

        return $config;
    }

    /**
     * Get config for date input and format
     *
     * @return array
     */
    protected function getDateConfig()
    {
        return [
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'change_month' => 'true',
            'change_year' => 'true',
            'show_on' => 'both',
            'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
        ];
    }

    /**
     * Get config for datetime input and format
     *
     * @return array
     */
    protected function getDatetimeConfig()
    {
        return [
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT,
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'time_format' => $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT),
            'change_month' => 'true',
            'change_year' => 'true',
            'show_on' => 'both',
            'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
        ];
    }

    /**
     * Build the option array for the config array
     *
     * @param OptionCollection $options
     * @return array
     */
    protected function getValuesConfig(OptionCollection $options)
    {
        $valuesConfig = [];

        foreach ($options as $option) {
            $valuesConfig[] = [
                'label' => $option->getTitle(),
                'value' => $option->getValue() == '' ? $option->getTitle() : $option->getValue(),
            ];
        }

        return $valuesConfig;
    }

    /**
     * Retrieve renderer for special custom field type
     *
     * @param string $fieldType
     * @return string
     */
    protected function getCustomFieldTypeRenderer($fieldType)
    {
        $renderers = $this->_fieldTypeSource->getCustomFieldsTypesRenderer();

        if (isset($renderers[$fieldType])) {
            $renderer = $renderers[$fieldType];
        } else {
            $renderer = null;
        }

        return $renderer;
    }

    /**
     * Build an array of data for the block
     *
     * @param CustomField $customField
     * @param Content $content
     * @return array
     */
    protected function _prepareDataBlock(CustomField $customField, Content $content = null)
    {
        $data = ['data' => []];

        $data['data']['custom_field'] = $customField;

        if (!empty($content)) {
            $data['data']['content_field'] = [
                $customField->getIdentifier() => $content->getData($customField->getIdentifier()),
            ];

            // Add specific data when the field is type of image
            if ($customField->getType() === 'image') {
                $key = $customField->getIdentifier() . '_orig';
                $data['data']['content_field'][$key] = $content->getData($key);
                $key = $customField->getIdentifier() . '_titl';
                $data['data']['content_field'][$key] = $content->getData($key);
                $key = $customField->getIdentifier() . '_url';
                $data['data']['content_field'][$key] = $content->getData($key);
                $key = $customField->getIdentifier() . '_alt';
                $data['data']['content_field'][$key] = $content->getData($key);
            }
        }

        return $data;
    }

    /**
     * Add additional fields by type of custom field
     *
     * @param Fieldset $fieldset
     * @param CustomField $customField
     * @return $this
     */
    protected function addAdditionalFields(Fieldset $fieldset, CustomField $customField)
    {
        // If type of image, add additional fields
        if ($customField->getType() === 'image') {
            if ($customField->getData(CustomField::IMG_TITLE)) {
                $fieldset->addField($customField->getIdentifier() . '_titl', 'text', [
                    'name' => $customField->getIdentifier() . '_titl',
                    'label' => __('%1 Attribute Title', $customField->getTitle()),
                    'title' => __('Image Attribute Title'),
                    'required' => $customField->getIsRequire(),
                ]);
            }
            if ($customField->getData(CustomField::IMG_ALT)) {
                $fieldset->addField($customField->getIdentifier() . '_alt', 'text', [
                    'name' => $customField->getIdentifier() . '_alt',
                    'label' => __('%1 Attribute Alt', $customField->getTitle()),
                    'title' => __('Image Attribute Alt'),
                    'required' => $customField->getIsRequire(),
                ]);
            }
            if ($customField->getData(CustomField::IMG_URL)) {
                $fieldset->addField($customField->getIdentifier() . '_url', 'text', [
                    'name' => $customField->getIdentifier() . '_url',
                    'label' => __('%1 Attribute Url', $customField->getTitle()),
                    'title' => __('Image Attribute Url'),
                    'required' => $customField->getIsRequire(),
                ]);
            }
        }

        return $this;
    }

    /**
     * Get default values for a custom field
     *
     * @param CustomField $customField
     * @param array $defaultValues
     * @return array
     */
    protected function getCustomFieldDefaultValue(CustomField $customField, array $defaultValues)
    {
        if (in_array($customField->getType(), ['drop_down', 'multiple', 'checkbox', 'radio'])) {
            // Set defaultValues for the custom field
            $defaultValues = array_merge(
                $defaultValues,
                $this->getOptionsDefaultValue(
                    $customField->getIdentifier(),
                    $customField->getOptionCollection(),
                    $defaultValues
                )
            );

            // If custom field is not a radio element set default values by option
            if ($customField->getType() !== 'radio') {
                if (!isset($defaultValues[$customField->getIdentifier()])) {
                    $defaultValues[$customField->getIdentifier()] = '';
                } else {
                    $defaultValues[$customField->getIdentifier()] = explode(
                        ',', $defaultValues[$customField->getIdentifier()]
                    );
                }
            }
        } elseif (!is_null($customField->getDefaultValue())) {
            if ($customField->getDefaultValue() === '<now>') {
                $customField->setDefaultValue($this->_localeDate->date());
            }
            $defaultValues[$customField->getIdentifier()] = $customField->getDefaultValue();
        }

        return $defaultValues;
    }

    /**
     * Get options default values for a custom field
     *
     * @param string $identifier
     * @param OptionCollection $optionsValue
     * @param array $defaultValues
     * @return array
     */
    protected function getOptionsDefaultValue($identifier, OptionCollection $optionsValue, array $defaultValues)
    {
        foreach ($optionsValue as $optionValue) {
            if ($optionValue->getDefault()) {
                // Default value set, use value
                if ($optionValue->getValue()) {
                    if (isset($defaultValues[$identifier])) {
                        $defaultValues[$identifier] .= ',' . $optionValue->getValue();
                    } else {
                        $defaultValues[$identifier] = $optionValue->getValue();
                    }
                    // No default value set, use title
                } else {
                    if (isset($defaultValues[$identifier])) {
                        $defaultValues[$identifier] .= ',' . $optionValue->getTitle();
                    } else {
                        $defaultValues[$identifier] = $optionValue->getTitle();
                    }
                }
            }
        }

        return $defaultValues;
    }
}
