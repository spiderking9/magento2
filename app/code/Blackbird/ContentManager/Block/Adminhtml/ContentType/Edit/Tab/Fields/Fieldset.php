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

use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Fieldset
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields
 */
class Fieldset extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/fields/fieldset.phtml';

    /**
     * @var int
     */
    protected $_itemCount = 1;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Blackbird\ContentManager\Model\ContentTypeFactory
     */
    protected $_contentTypeFactory;

    /**
     * @var ContentType
     */
    protected $_contenttypeInstance;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type
     */
    protected $_sourceFieldTypes;

    /**
     * Fieldset constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Blackbird\ContentManager\Model\ContentTypeFactory $contentTypeFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $sourceFieldType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Blackbird\ContentManager\Model\ContentTypeFactory $contentTypeFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $sourceFieldType,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_contentTypeFactory = $contentTypeFactory;
        $this->_sourceFieldTypes = $sourceFieldType;
        parent::__construct($context, $data);
    }

    /**
     * Get add button html
     *
     * @return string
     */
    public function getAddFieldButtonHtml()
    {
        return $this->getChildHtml('add_field');
    }

    /**
     * Get block field html
     *
     * @return string
     */
    public function getFieldBlockHtml()
    {
        return $this->getChildHtml('field_block');
    }

    /**
     * Get fieldset name
     *
     * @return string
     */
    public function getFieldsetName()
    {
        return 'contenttype[fieldsets]';
    }

    /**
     * Get fieldset id
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return 'contenttype_fieldset';
    }

    /**
     * Get item count
     *
     * @return int
     */
    public function getItemCount()
    {
        return $this->_itemCount;
    }

    /**
     * Get the custom fieldset values
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getFieldsetValues()
    {
        $values = [];
        $fieldsets = $this->getContentType()->getCustomFieldsetCollection();

        foreach ($fieldsets as $fieldset) {
            $value = [
                'fieldset_id' => $fieldset->getId(),
                'title' => $fieldset->getTitle(),
                'sort_order' => $fieldset->getSortOrder(),
            ];

            $values[] = new \Magento\Framework\DataObject($value);
        }

        return $values;
    }

    /**
     * Get current contenttype
     *
     * @return ContentType
     */
    public function getContentType()
    {
        if (!$this->_contenttypeInstance) {
            $contenttype = $this->_coreRegistry->registry('current_contenttype');

            if ($contenttype) {
                $this->_contenttypeInstance = $contenttype;
            } else {
                $this->_contenttypeInstance = $this->_contentTypeFactory->create();
            }
        }

        return $this->_contenttypeInstance;
    }

    /**
     * Get the custom field values
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getFieldValues()
    {
        $values = [];
        $value = [];
        $fields = $this->getContentType()->getCustomFieldCollection();

        foreach ($fields as $field) {
            $value['field_uid'] = $field->getId();
            $value['fieldset_id'] = $field->getFieldsetId();
            $value['title'] = $field->getTitle();
            $value['identifier'] = $field->getIdentifier();
            $value['sort_order'] = $field->getSortOrder();
            $value['type'] = $field->getType();
            $value['note'] = $field->getNote();
            $value['default_value'] = $field->getDefaultValue();
            $value['is_require'] = $field->getIsRequire();
            $value['show_in_grid'] = $field->getShowInGrid();
            $value['max_characters'] = $field->getMaxCharacters();
            $value['wysiwyg_editor'] = $field->getWysiwygEditor();
            $value['crop'] = $field->getCrop();
            $value['crop_w'] = $field->getCropW();
            $value['crop_h'] = $field->getCropH();
            $value['keep_aspect_ratio'] = $field->getKeepAspectRatio();
            $value['file_path'] = $field->getFilePath();
            $value['img_alt'] = $field->getImgAlt();
            $value['img_url'] = $field->getImgUrl();
            $value['img_title'] = $field->getImgTitle();
            $value['file_extension'] = $field->getFileExtension();
            $value['content_type'] = $field->getData('content_type');
            $value['attribute'] = $field->getData('attribute');
            $value['is_searchable'] = $field->getEavAttribute()->getIsSearchable();
            $value['search_weight'] = $field->getEavAttribute()->getSearchWeight();

            $values[] = new \Magento\Framework\DataObject($value);
        }

        return $values;
    }

    /**
     * Get the custom field options values
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getOptionValues()
    {
        $values = [];
        $fields = $this->getContentType()->getCustomFieldCollection();

        foreach ($fields as $field) {
            foreach ($field->getOptionCollection() as $option) {
                $value = [
                    'fieldset_id' => $field->getFieldsetId(),
                    'field_uid' => $field->getId(),
                    'select_uid' => $option->getId(),
                    'title' => $option->getTitle(),
                    'value' => $option->getValue(),
                    'default_val' => $option->getDefault(),
                    'sort_order' => $option->getSortOrder(),
                ];

                $values[] = new \Magento\Framework\DataObject($value);
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_field', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Add New Field'),
            'class' => 'add',
            'id' => 'contenttype_fieldset_<%- data.id %>_add_new_custom_field',
        ]);

        $this->addChild('field_block', 'Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Field');

        parent::_prepareLayout();
    }
}
