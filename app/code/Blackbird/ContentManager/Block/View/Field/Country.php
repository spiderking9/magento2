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
 * Class Country
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class Country extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_countryCollection;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionInstance;

    /**
     * Country constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollection,
        array $data = []
    ) {
        $this->_countryCollection = $countryCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get the country collection
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    public function getCountryCollection()
    {
        return $this->_getCountryCollectionInstance()->addFieldToSelect('*')->addCountryCodeFilter($this->getContent()
            ->getDataAsArray($this->getIdentifier()), 'iso2');
    }

    /**
     * Retrieve Country collection instance
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected function _getCountryCollectionInstance()
    {
        if (!$this->_countryCollectionInstance) {
            $this->_countryCollectionInstance = $this->_countryCollection->create();
        }

        return $this->_countryCollectionInstance;
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

        // Test applying content/view/"content type"/field/country/"country type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/country/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/country/"country type".phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/country/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/country/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/country/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }
}