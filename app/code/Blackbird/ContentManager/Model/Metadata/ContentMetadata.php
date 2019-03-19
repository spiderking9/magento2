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

namespace Blackbird\ContentManager\Model\Metadata;

use Blackbird\ContentManager\Api\ContentMetadataInterface;
use Blackbird\ContentManager\Model\AttributeMetadataConverter;
use Blackbird\ContentManager\Model\AttributeMetadataDataProvider;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Service to fetch content related custom attributes
 *
 * Class ContentMetadata
 *
 * @package Blackbird\ContentManager\Model\Metadata
 */
class ContentMetadata implements ContentMetadataInterface
{
    /**
     * @var array
     */
    protected $_contentDataObjectMethods;

    /**
     * @var AttributeMetadataConverter
     */
    protected $_attributeMetadataConverter;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $_attributeMetadataDataProvider;

    /**
     * ContentMetadata constructor
     *
     * @param AttributeMetadataConverter $attributeMetadataConverter
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        AttributeMetadataConverter $attributeMetadataConverter,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->_attributeMetadataConverter = $attributeMetadataConverter;
        $this->_attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $attributes = [];
        $attributesFormCollection = $this->_attributeMetadataDataProvider->loadAttributesCollection(self::ENTITY_TYPE_CONTENT,
            $formCode);
        foreach ($attributesFormCollection as $attribute) {
            /** @var $attribute \Blackbird\ContentManager\Model\Attribute */
            $attributes[$attribute->getAttributeCode()] = $this->_attributeMetadataConverter->createMetadataAttribute($attribute);
        }
        if (empty($attributes)) {
            throw NoSuchEntityException::singleField('formCode', $formCode);
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        $customAttributes = [];
        $dataObjectClassName = $dataObjectClassName ?: self::DATA_INTERFACE_NAME;
        if (!$this->_contentDataObjectMethods) {
            $dataObjectMethods = array_flip(get_class_methods($dataObjectClassName));
            $baseClassDataObjectMethods = array_flip(get_class_methods('Magento\Framework\Api\AbstractExtensibleObject'));
            $this->_contentDataObjectMethods = array_diff_key($dataObjectMethods, $baseClassDataObjectMethods);
        }
        foreach ($this->getAllAttributesMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            $camelCaseKey = SimpleDataObjectConverter::snakeCaseToUpperCamelCase($attributeCode);
            $isDataObjectMethod = isset($this->_contentDataObjectMethods['get' . $camelCaseKey]) || isset($this->_contentDataObjectMethods['is' . $camelCaseKey]);

            /** Even though disable_auto_group_change is system attribute, it should be available to the clients */
            if (!$isDataObjectMethod && (!$attributeMetadata->isSystem() || $attributeCode == 'disable_auto_group_change')) {
                $customAttributes[] = $attributeMetadata;
            }
        }

        return $customAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributesMetadata()
    {
        /** @var AbstractAttribute[] $attribute */
        $attributeCodes = $this->_attributeMetadataDataProvider->getAllAttributeCodes(self::ENTITY_TYPE_CONTENT,
            self::ATTRIBUTE_SET_ID_CONTENT);

        $attributesMetadata = [];

        foreach ($attributeCodes as $attributeCode) {
            try {
                $attributesMetadata[] = $this->getAttributeMetadata($attributeCode);
            } catch (NoSuchEntityException $e) {
                //If no such entity, skip
                continue;
            }
        }

        return $attributesMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->_attributeMetadataDataProvider->getAttribute(self::ENTITY_TYPE_CONTENT, $attributeCode);
        if ($attribute && ($attributeCode === 'id' || $attribute->getId() !== null)) {
            $attributeMetadata = $this->_attributeMetadataConverter->createMetadataAttribute($attribute);

            return $attributeMetadata;
        } else {
            throw new NoSuchEntityException(__(NoSuchEntityException::MESSAGE_DOUBLE_FIELDS, [
                'fieldName' => 'entityType',
                'fieldValue' => self::ENTITY_TYPE_CONTENT,
                'field2Name' => 'attributeCode',
                'field2Value' => $attributeCode,
            ]));
        }
    }
}
