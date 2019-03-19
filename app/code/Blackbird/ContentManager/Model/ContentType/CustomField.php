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

namespace Blackbird\ContentManager\Model\ContentType;

use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ContentType\CustomField\Option;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField as ResourceCustomField;
use Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute;

/**
 * Custom Field Model
 *
 * Class CustomField
 *
 * @package Blackbird\ContentManager\Model\ContentType
 * @method int getId() Get Id of Custom Fields
 */
class CustomField extends \Blackbird\ContentManager\Model\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentType\CustomFieldInterface
{
    /**
     * @var ContentType
     */
    protected $contenttype;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option\CollectionFactory
     */
    protected $_optionCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type
     */
    protected $_sourceFieldTypes;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Attribute
     */
    protected $_attribute = null;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_eavEntityType = null;

    /**
     * CustomField constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $sourceFieldTypes
     * @param ResourceCustomField\Option\CollectionFactory $optionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Attribute $attribute
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $sourceFieldTypes,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Blackbird\ContentManager\Model\Attribute $attribute,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_modelFactory = $modelFactory;
        $this->_sourceFieldTypes = $sourceFieldTypes;
        $this->_optionCollectionFactory = $optionCollectionFactory;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_attribute = $attribute;
    }

    /**
     * Get fieldset id
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_getData(self::FIELDSET_ID);
    }

    /**
     * Get sort order
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->_getData(self::SORT_ORDER);
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->_getData(self::NOTE);
    }

    /**
     * Get a text for option value
     *
     * @todo refactor
     * @param mixed $value
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionText($value)
    {
        $optionText = false;

        if (in_array($this->getType(), ['content', 'attribute'])) {
            $attributeCode = $this->getContentType() ?: $this->getAttribute();
            $attribute = $this->_attributeCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->getFirstItem();
            $optionText = $attribute->getSource()->getOptionText($value);
        } elseif ($this->getType() === 'category') {
            $category = $this->_modelFactory->create(\Magento\Catalog\Model\Category::class)
                ->getCollection()
                ->addFieldToSelect('name')
                ->addFieldToFilter('entity_id', $value);
            $optionText = $category->count() ? $category->getFirstItem()->getName() : false;
        } elseif ($this->getEavAttribute()->getSourceModel()) {
            $optionText = $this->getEavAttribute()->getSource()->getOptionText($value);
        } else {
            $options = $this->getAllOptions();

            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    $optionText = isset($option['label']) ? $option['label'] : $option['value'];
                }
            }
        }

        return $optionText;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * Retrieve contenttype instance
     *
     * @todo check usage
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contenttype;
    }

    /**
     * Set contenttype instance
     *
     * @todo check usage
     * @param ContentType $contenttype
     * @return $this
     */
    public function setContentType(ContentType $contenttype)
    {
        $this->contenttype = $contenttype;

        return $this;
    }

    /**
     * Retrieve the linked attribute
     *
     * @return \Blackbird\ContentManager\Model\Attribute
     */
    public function getEavAttribute()
    {
        if (!empty($this->getAttributeId())) {
            $this->_attribute->load($this->getAttributeId());
        }

        return $this->_attribute;
    }

    /**
     * Get all options
     *
     * @todo refactor
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllOptions()
    {
        $options = [];

        if ($this->getType() === 'attribute') {
            /** @var \Magento\Eav\Model\Attribute $attribute */
            $attribute = $this->_attributeCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('attribute_code', $this->getData('attribute'))
                ->getFirstItem();
            $options = $attribute->getSource()->getAllOptions();
        } elseif ($this->getType() === 'category') {
            $categories = $this->_modelFactory->create(\Magento\Catalog\Model\Category::class)
                ->getCollection()
                ->addFieldToSelect(['entity_id', 'name']);
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($categories as $category) {
                $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
            }
        } elseif ($this->getEavAttribute()->getSourceModel()) {
            $options = $this->getEavAttribute()->getSource()->getAllOptions();
        } else {
            if ($this->getOptionCollection()) {
                foreach ($this->getOptionCollection() as $option) {
                    $options[] = ['label' => $option->getTitle(), 'value' => $option->getValue()];
                }
            }
        }

        return $options;
    }

    /**
     * Returns all option of the custom field
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option\Collection
     */
    public function getOptionCollection()
    {
        $collection = $this->_optionCollectionFactory->create()
            ->addFieldToFilter(Option::FIELD_ID, $this->getId())
            ->addTitleToResult()
            ->setOrder(Option::SORT_ORDER, 'asc');

        return $collection;
    }

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        // Delete options
        $this->deleteOptions();

        // Delete attributes
        $this->deleteEavAttribute();

        return $this;
    }

    /**
     * Delete options for a select field
     */
    protected function deleteOptions()
    {
        foreach ($this->getOptionCollection() as $option) {
            $option->delete();
        }
    }

    /**
     * Delete the eav attribute of the custom field
     */
    protected function deleteEavAttribute()
    {
        $attribute = $this->getEavAttribute();

        if ($attribute) {
            // Delete image attributes
            if ($this->getType() === 'image') {
                $this->deleteImageFieldAttributes();
            }

            $attribute->delete();
        }
    }

    /**
     * Delete the image attributes
     */
    protected function deleteImageFieldAttributes()
    {
        $attributes = $this->_attributeCollectionFactory->create()->addFieldToFilter('attribute_code', [
            'in' => [
                $this->getIdentifier() . '_orig',                               // get the original filename
                $this->getIdentifier() . '_alt',
                $this->getIdentifier() . '_url',
                $this->getIdentifier() . '_titl',
            ],
        ])->addFieldToFilter('entity_type_id', $this->getEavEntityType()->getEntityTypeId());

        foreach ($attributes as $attribute) {
            $attribute->delete();
        }
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_getData(self::IDENTIFIER);
    }

    /**
     * Retrieve the entity type of Advanced Content Manager
     *
     * @return \Magento\Eav\Model\Entity\Type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEavEntityType()
    {
        if ($this->_eavEntityType === null) {
            $this->_eavEntityType = $this->_modelFactory->get('Magento\Eav\Model\Entity\Type')
                ->load(\Blackbird\ContentManager\Model\Content::ENTITY, 'entity_type_code');
        }

        return $this->_eavEntityType;
    }

    /**
     * Processing object before save data
     *
     * @return $this|void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        // Save EAV Attribute
        $this->saveEavAttribute();
    }

    /**
     * Create or update the eav attribute
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveEavAttribute()
    {
        if (!empty($this->getId()) && !empty($this->getAttributeId())) {
            $this->updateEavAttribute($this->getIsSearchable(), $this->getSearchWeight());
        } else {
            $attribute = $this->createEavAttribute($this->getTitle(), $this->getIdentifier(), $this->getType(),
                $this->getIsSearchable(), $this->getSearchWeight());

            $this->setAttributeId($attribute->getAttributeId());
        }
    }

    /**
     * Update an EAV Attribte
     *
     * @param bool $isSearchable
     * @param int $attributeSearchWeight
     * @return \Blackbird\ContentManager\Model\Attribute
     * @throws \Exception
     */
    protected function updateEavAttribute($isSearchable, $attributeSearchWeight)
    {
        $attribute = $this->getEavAttribute();
        $attribute->setData('is_searchable', $isSearchable);
        $attribute->setData('search_weight', $attributeSearchWeight);
        $attribute->save();

        return $attribute;
    }

    /**
     * Create an EAV Attribute
     *
     * @param string $title
     * @param string $identifier
     * @param string $type
     * @param bool $isSearchable
     * @param null $searchWeight
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createEavAttribute($title, $identifier, $type, $isSearchable = false, $searchWeight = null)
    {
        $contentAttribute = $this->_modelFactory->create(Attribute::class);
        $backendModel = null;

        // Specials backend models by type
        if (in_array($type, ['date', 'date_time'])) {
            $backendModel = \Blackbird\ContentManager\Model\Entity\Attribute\Backend\Datetime::class;
        }

        // Attribute definition
        $attribute = [
            'entity_type_id' => $this->getEavEntityType()->getEntityTypeId(),
            'attribute_code' => $identifier,
            'backend_model' => $backendModel,
            'backend_type' => $contentAttribute->getBackendTypeByInput($type),
            'frontend_input' => 'text',
            'frontend_label' => $title,
            'is_required' => false,
            'is_user_defined' => false,
            'is_global' => 0,
            'is_searchable' => $isSearchable,
            'search_weight' => $searchWeight,
            'is_visible' => true,
        ];

        $contentAttribute->setData($attribute);
        $contentAttribute->save();

        // If it's an image type, we add specific attributes
        if ($type === 'image') {
            $this->addImageAttributes($title, $identifier);
        }

        return $contentAttribute;
    }

    /**
     * Add attributes for image field type
     *
     * @param string $title
     * @param string $identifier
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addImageAttributes($title, $identifier)
    {
        $attrs = [
            '_orig' => [
                'label' => __(' - Original Image'),
                'type' => ('image_original'),
            ],
            '_alt' => [
                'label' => __(' - Image ALT'),
                'type' => 'image_alt',
            ],
            '_url' => [
                'label' => __(' - Image URL'),
                'type' => 'img_url',
            ],
            '_titl' => [
                'label' => __(' - Image TITLE'),
                'type' => 'img_titl',
            ],
        ];

        foreach ($attrs as $code => $attr) {
            $this->createEavAttribute($title . $attr['label'], $identifier . $code, $attr['type']);
        }
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return __($this->_getData(self::TITLE))->render();
    }

    /**
     * Processing object after save data
     *
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();

        // Save options
        $this->saveOptions();

        return $this;
    }

    /**
     * Save options for a field of type of select
     */
    protected function saveOptions()
    {
        if (!empty($this->getOptions()) && is_array($this->getOptions())) {
            foreach ($this->getOptions() as $option) {
                if (!is_array($option)) {
                    continue;
                }

                $optionModel = $this->_modelFactory->create(Option::class);
                $optionModel->setData($option)->setData(Option::FIELD_ID, $this->getId());

                // Create new option
                if ($optionModel->getData(Option::ID) < '1') {
                    $optionModel->unsetData(Option::ID);
                }

                // Delete option if is no more or save
                if ($optionModel->getData('is_delete') == '1') {
                    $optionModel->delete();
                } else {
                    $optionModel->save();
                }
            }
        }
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_getData(self::OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceCustomField::class);
        $this->setIdFieldName(self::ID);
    }
}
