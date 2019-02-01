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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab;

/**
 * Class Main
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enabledisable;

    /**
     * Main constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
        $this->_enabledisable = $enabledisable;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content Type Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Content Type Information');
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        /** Information */

        $fieldset = $form->addFieldset('informations_fieldset', ['legend' => __('Content Type Information')]);

        $fieldset->addField('title', 'text', [
            'name' => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
            'required' => true,
        ]);

        $fieldset->addField('identifier', 'text', [
            'name' => 'identifier',
            'label' => __('Identifier'),
            'title' => __('Identifier'),
            'class' => 'validate-identifier',
            'required' => true,
        ]);

        $fieldset->addField('default_status', 'select', [
            'name' => 'default_status',
            'label' => __('Default Status'),
            'title' => __('Default Status'),
            'required' => false,
            'values' => $this->_enabledisable->toOptionArray(),
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'tile' => __('Description'),
            'required' => false,
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_informations_prepareform',
            ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
