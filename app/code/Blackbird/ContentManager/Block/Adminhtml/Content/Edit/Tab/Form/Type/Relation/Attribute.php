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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation;

use Blackbird\ContentManager\Api\Data\ContentType\CustomFieldInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Build renderer as the core field type 'attribute'.
 *
 * Class Attribute
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation
 */
class Attribute extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\AbstractType
{
    /**
     * Default template for attribute field
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit/tab/form/type/relation/attribute.phtml';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $_customFieldsSource;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Attribute constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_customFieldsSource = $customFieldsSource;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the class attribute of the field, depending of the type
     *
     * @return string
     */
    public function getHtmlClass()
    {
        $class = ' admin__control-select select';

        if ($this->getCustomField()->getMaxCharacters()) {
            $class = ' multiselect select admin__control-multiselect';
        }

        return $class;
    }

    /**
     * Retrieve the name attribute of the field, depending of the type
     *
     * @return string
     */
    public function getHtmlName()
    {
        $name = $this->getElement()->getName();

        if ($this->getCustomField()->getMaxCharacters()) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Retrieve the additional attributes of the field, depending of the type
     *
     * @return string
     */
    public function getHtmlAdditionalAttributes()
    {
        $attr = '';

        if ($this->getCustomField()->getMaxCharacters()) {
            $attr = 'size=10 multiple="multiple"';
        }

        return $attr;
    }

    /**
     * Retrieve all values for an attribute
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllOptions()
    {
        // Retrieve attribute of the current field element
        $customField = $this->_customFieldsSource->getCustomFieldsByIdentifiers($this->getElement()->getName())
            ->getFirstItem();
        $code = null;

        if ($customField) {
            $code = $customField->getData(CustomFieldInterface::ATTRIBUTE);
        }

        $attribute = $this->_eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $code);
        $options = $attribute->getSource()->getAllOptions();

        return $options;
    }
}
