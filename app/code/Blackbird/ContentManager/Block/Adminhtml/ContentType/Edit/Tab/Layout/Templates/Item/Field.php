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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item;

/**
 * Class Field
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item
 */
class Field extends AbstractItem
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/layout/templates/item/field.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $_customFields;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type
     */
    protected $_fieldType;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Field constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFields
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldType
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFields,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields\Type $fieldType,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $labelOptions, $data);
        $this->_customFields = $customFields;
        $this->_fieldType = $fieldType;
        $this->_coreRegistry = $registry;
    }

    /**
     * Get content type custom fields
     *
     * @return array
     */
    public function getCustomFields()
    {
        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $return = $contentType ? $this->_customFields->toArray($contentType->getCtId()) : [];

        return $return;
    }

    /**
     * Retrieve html templates form for different types
     *
     * @return string
     */
    public function getTemplateFieldsTypeHtml()
    {
        $types = $this->_fieldType->getCustomFieldsOptionRenderer();
        $templates = '';

        foreach ($types as $type) {
            $templates .= $this->getChildHtml($type['name']) . PHP_EOL;
        }

        return $templates;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $types = $this->_fieldType->getCustomFieldsOptionRenderer();

        foreach ($types as $type) {
            $this->addChild($type['name'], $type['renderer']);
        }

        parent::_prepareLayout();
    }
}
