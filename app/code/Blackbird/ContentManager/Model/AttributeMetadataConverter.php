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

namespace Blackbird\ContentManager\Model;

use Blackbird\ContentManager\Api\Data\AttributeMetadataInterfaceFactory;
use Blackbird\ContentManager\Api\Data\OptionInterfaceFactory;

/**
 * Converter for AttributeMetadata
 *
 * Class AttributeMetadataConverter
 *
 * @package Blackbird\ContentManager\Model+
 */
class AttributeMetadataConverter
{
    /**
     * @var OptionInterfaceFactory
     */
    protected $_optionFactory;

    /**
     * @var AttributeMetadataInterfaceFactory
     */
    protected $_attributeMetadataFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * Initialize the Converter
     *
     * @param OptionInterfaceFactory $optionFactory
     * @param AttributeMetadataInterfaceFactory $attributeMetadataFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        OptionInterfaceFactory $optionFactory,
        AttributeMetadataInterfaceFactory $attributeMetadataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->_optionFactory = $optionFactory;
        $this->_attributeMetadataFactory = $attributeMetadataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Create AttributeMetadata Data object from the Attribute Model
     *
     * @param \Blackbird\ContentManager\Model\Attribute $attribute
     * @return \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMetadataAttribute($attribute)
    {
        $options = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $optionDataObject = $this->_optionFactory->create();
                if (!is_array($option['value'])) {
                    $optionDataObject->setValue($option['value']);
                } else {
                    $optionArray = [];
                    foreach ($option['value'] as $optionArrayValues) {
                        $optionObject = $this->_optionFactory->create();
                        $this->_dataObjectHelper->populateWithArray($optionObject, $optionArrayValues,
                            '\Blackbird\ContentManager\Api\Data\OptionInterface');
                        $optionArray[] = $optionObject;
                    }
                    $optionDataObject->setOptions($optionArray);
                }
                $optionDataObject->setLabel($option['label']);
                $options[] = $optionDataObject;
            }
        }


        return $this->_attributeMetadataFactory->create()
            ->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter((string)$attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setIsVisible((boolean)$attribute->getIsVisible())
            ->setIsRequired((boolean)$attribute->getIsRequired())
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setNote((string)$attribute->getNote())
            ->setIsSystem((boolean)$attribute->getIsSystem())
            ->setIsUserDefined((boolean)$attribute->getIsUserDefined())
            ->setBackendType($attribute->getBackendType())
            ->setSortOrder((int)$attribute->getSortOrder());
    }
}
