<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Model\Menu\NodeTypes\Config;

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
        foreach ($source->getElementsByTagName('node_type') as $node) {
            $nodeType = $this->_getAttributeValue($node, 'name');
            $data = [];
            $data['name'] = $nodeType;
            $data['label'] = $this->_getAttributeValue($node, 'label');
            $data['renderer_front'] = $this->_getAttributeValue($node, 'renderer_front');
            $data['renderer_admin'] = $this->_getAttributeValue($node, 'renderer_admin');
            $data['dependencies'] = null;

            /** @var $childNode \DOMNode */
            foreach ($node->childNodes as $module_dependencies) {
                if ($module_dependencies->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $dependencies = [];

                foreach ($module_dependencies->childNodes as $dependency) {
                    if ($dependency->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }

                    $module_dependency = [
                        'module_name' => $this->_getAttributeValue($dependency, 'module_name'),
                        'version' => $this->_getAttributeValue($dependency, 'version'),
                    ];
                    $dependencies[] = $module_dependency;

                }

                $data['dependencies'] = $dependencies;
            }


            $output[$nodeType] = $data;
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
