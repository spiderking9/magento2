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

namespace Blackbird\ContentManager\Model\Config\Source\Product;

/**
 * Class Attributes
 *
 * @package Blackbird\ContentManager\Model\Config\Source\Product
 */
class Attributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_paCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $_paCollectionInstance;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Attributes constructor
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $paCollection
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $paCollection,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->_productModel = $productModel;
        $this->_paCollection = $paCollection;
        $this->_escaper = $escaper;
    }

    /**
     * Get option array product
     *
     * @return array
     */
    public function toOptionArrayProduct()
    {
        $array = [];

        foreach ($this->_productModel->getAttributes() as $attribute) {
            if (in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                $array[] = [
                    'label' => $attribute->getAttributeCode() . ' (' . $this->_escaper->escapeQuote($attribute->getFrontendLabel(),
                            true) . ')',
                    'value' => $attribute->getAttributeCode(),
                ];
            }
        }

        return $array;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = [];

        foreach ($this->getProductAttributeCollection() as $attribute) {
            if (in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                $array[] = [
                    'label' => $attribute->getAttributeCode() . ' (' . $this->_escaper->escapeQuote($attribute->getFrontendLabel(),
                            true) . ')',
                    'value' => $attribute->getAttributeCode(),
                ];
            }
        }

        return $array;
    }

    /**
     * Return the product attribute collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getProductAttributeCollection()
    {
        return $this->_getPaCollectionInstance();
    }

    /**
     * Rerieve product attributes collection instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected function _getPaCollectionInstance()
    {
        if (!$this->_paCollectionInstance) {
            $this->_paCollectionInstance = $this->_paCollection->create();
        }

        return $this->_paCollectionInstance;
    }
}
