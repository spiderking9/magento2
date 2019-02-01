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
 * Class Customer
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class Customer extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected $_customerCollectionInstance;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerCollection;

    /**
     * Customer constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        array $data = []
    ) {
        $this->_customerCollection = $customerCollection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the content collection instance
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getCustomerCollectionInstance()
    {
        if ($this->_customerCollectionInstance === null) {
            $this->_customerCollectionInstance = $this->getCustomerCollection(['entity_id']);
        }

        return $this->_customerCollectionInstance;
    }

    /**
     * Return the collection of the customers
     *
     * @param array $attributes
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerCollection(array $attributes)
    {
        $collection = $this->_customerCollection->create()
            ->addAttributeToSelect($attributes)
            ->addAttributeToFilter('entity_id', $this->getContent()->getDataAsArray($this->getIdentifier()) ?: '-1');

        return $collection;
    }

    /**
     * @todo move to abstract generic class
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = $this->getType();

        // Test applying content/view/"content type"/field/customer/"content type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/customer/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/customer/"content type.phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/customer/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/customer/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/customer/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }
}
