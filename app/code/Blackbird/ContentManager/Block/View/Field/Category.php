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

namespace Blackbird\ContentManager\Block\View\Field;

/**
 * Class Category
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class Category extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionInstance;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        array $data = []
    ) {
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get the category collection
     *
     * @param array $attributes
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection(array $attributes)
    {
        return $this->_getCategoryCollectionInstance()
            ->addAttributeToSelect(array_merge($attributes, ['name', 'url_key']))
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('entity_id', $this->getContent()->getDataAsArray($this->getIdentifier()) ?: '-1');
    }

    /**
     * Retrieve Category collection instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected function _getCategoryCollectionInstance()
    {
        if (!$this->_categoryCollectionInstance) {
            $this->_categoryCollectionInstance = $this->_categoryCollection->create();
        }

        return $this->_categoryCollectionInstance;
    }

    /**
     * @todo move to abstract generic class
     * @return $this
     */
    protected function _prepareLayout()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = $this->getType();

        // Test applying content/view/"content type"/field/category/"category type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/category/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/category/"category type".phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/category/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/category/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/category/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }
}
