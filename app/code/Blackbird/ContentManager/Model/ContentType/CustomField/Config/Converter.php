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

namespace Blackbird\ContentManager\Model\ContentType\CustomField\Config;

/**
 * Class Converter
 *
 * @package Blackbird\ContentManager\Model\ContentType\CustomField\Config
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];

        /** @var $fieldNode \DOMNode */
        foreach ($source->getElementsByTagName('field') as $fieldNode) {
            $fieldName = $this->_getAttributeValue($fieldNode, 'name');
            $data = [];
            $data['name'] = $fieldName;
            $data['label'] = $this->_getAttributeValue($fieldNode, 'label');
            $data['renderer'] = $this->_getAttributeValue($fieldNode, 'renderer');
            $data['option_renderer'] = $this->_getAttributeValue($fieldNode, 'optionRenderer');

            /** @var $childNode \DOMNode */
            foreach ($fieldNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $inputTypeName = $this->_getAttributeValue($childNode, 'name');
                $data['types'][$inputTypeName] = [
                    'name' => $inputTypeName,
                    'label' => $this->_getAttributeValue($childNode, 'label'),
                    'renderer' => $this->_getAttributeValue($childNode, 'renderer'),
                    'disabled' => 'true' == $this->_getAttributeValue($childNode, 'disabled', 'false') ? true : false,
                    'is_repeater_compatible' => 'true' == $this->_getAttributeValue($childNode, 'is_repeater_compatible', 'false') ? true : false,
                ];
            }
            $output[$fieldName] = $data;
        }

        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $node
     * @param string $attributeName
     * @param string|null $defaultValue
     * @return null|string
     */
    protected function _getAttributeValue(\DOMNode $node, $attributeName, $defaultValue = null)
    {
        $attributeNode = $node->attributes->getNamedItem($attributeName);
        $output = $defaultValue;
        if ($attributeNode) {
            $output = $attributeNode->nodeValue;
        }

        return $output;
    }
}
