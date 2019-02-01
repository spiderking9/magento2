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

namespace Blackbird\ContentManager\Model\Rule\Condition;

use Blackbird\ContentManager\Model\ResourceModel\Content\Collection;

/**
 * ContentManager Rule Content Condition data model
 *
 * Class Content
 *
 * @package Blackbird\ContentManager\Model\Rule\Condition
 */
class Content extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content
     */
    protected $_contentResource;

    /**
     * @var string
     */
    protected $elementName = 'parameters';

    /**
     * Content constructor
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content $contentResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content $contentResource,
        array $data = []
    ) {
        $this->_contentResource = $contentResource;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $contentAttributes = $this->_contentResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($contentAttributes as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return parent::loadAttributeOptions();
    }

    /**
     * Retrieve the current attribute object
     *
     * @return \Magento\Eav\Model\Attribute
     */
    public function getAttributeObject()
    {
        if (!$this->hasData('attribute_object')) {
            $this->setData('attribute_object', $this->_contentResource->getAttribute($this->getAttribute()));
        }

        return $this->getData('attribute_object');
    }

    /**
     * Add the attribute to the collection
     *
     * @param Collection $collection
     * @return \Blackbird\ContentManager\Model\Rule\Condition\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addToCollection(Collection $collection)
    {
        if (!in_array($this->getAttribute(), ['status'])) {
            $collection->addAttributeToSelect($this->getAttribute(), 'left');
        }

        return $this;
    }

    /**
     * Get mapped sql field
     *
     * @return string
     */
    public function getMappedSqlField()
    {
        return $this->getEavAttributeTableAlias() . '.value';
    }

    /**
     * Get eav attribute alias
     *
     * @return string
     */
    protected function getEavAttributeTableAlias()
    {
        return 'at_' . $this->getAttribute();
    }

    /**
     * Has default field
     *
     * @return bool
     */
    public function hasDefaultValue()
    {
        return true;
    }
}
