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

namespace Blackbird\ContentManager\Model\Layouts\Config;

/**
 * Class Converter
 *
 * @package Blackbird\ContentManager\Model\Layouts\Config
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

        /** @var $layouts \DOMNode */
        foreach ($source->getElementsByTagName('layouts') as $layouts) {
            $layoutsId = $this->_getAttributeValue($layouts, 'id');
            $data = [];
            $data['id'] = $layoutsId;
            $data['label'] = $this->_getAttributeValue($layouts, 'label');

            /** @var $layout \DOMNode */
            foreach ($layouts->childNodes as $layout) {
                if ($layout->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $layoutId = $this->_getAttributeValue($layout, 'id');
                $data['layout'][$layoutId] = [
                    'id' => $layoutId,
                    'label' => $this->_getAttributeValue($layout, 'label'),
                    'disabled' => 'true' == $this->_getAttributeValue($layout, 'disabled', 'false') ? true : false,
                    'column' => null,
                ];
                $columns = [];

                /** @var $column \DOMNode */
                foreach ($layout->childNodes as $column) {
                    if ($column->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $columnId = $this->_getAttributeValue($column, 'id');
                    $columns[$columnId] = [
                        'id' => $columnId,
                        'class' => $this->_getAttributeValue($column, 'class'),
                        'width' => $this->_getAttributeValue($column, 'width'),
                        'float' => $this->_getAttributeValue($column, 'float'),
                    ];
                }
                $data['layout'][$layoutId]['column'] = $columns;
            }
            $output[$layoutsId] = $data;
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
