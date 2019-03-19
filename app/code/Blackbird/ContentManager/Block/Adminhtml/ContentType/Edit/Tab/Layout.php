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
 * Class Layout
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Layout extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $_pageLayoutBuilder;

    /**
     * Layout constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_pageLayoutBuilder = $pageLayoutBuilder;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Layout');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Layout');
    }

    /**
     * Can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
     *
     * @return boolean
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
        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $pageLayout = $this->_pageLayoutBuilder->getPageLayoutsConfig();
        $layout = ($contentType) ? $contentType->getLayout() : '';

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        /** Layout */

        $fieldset = $form->addFieldset('layout_fieldset', ['legend' => __('Step 1 - Select your layout')]);

        $fieldset->addField('root_template', 'select', [
            'name' => 'layout[general]',
            'label' => 'Layout General',
            'title' => 'Layout General',
            'required' => true,
            'note' => __('Modify general layout'),
            'values' => $pageLayout->toOptionArray(),
        ]);

        $fieldset->addField('layout_update_xml', 'textarea', [
            'name' => 'layout[xml]',
            'label' => __('Layout Update XML'),
            'title' => __('Layout Update XML'),
        ]);

        $fieldset->addField('layout', 'text', [
            'name' => 'layout[template]',
            'label' => __('Content layout'),
            'title' => __('Content layout'),
            'note' => __('Select to preview the content layout'),
            'value' => $layout,
        ])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Layouts'));

        /** Layout items */

        $fieldset = $form->addFieldset('layout_items_fieldset', [
            'legend' => __('Step 2 - Drag and drop items in your layout'),
            'class' => 'layout-manager-items',
        ]);

        $fieldset->addField('layout_items', 'text', [
            'name' => 'layout_items',
            'label' => __('Layout items'),
            'title' => __('Layout items'),
        ])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Items'));

        /** Layout grid */

        $fieldset = $form->addFieldset('layout_grid_fieldset', [
            'legend' => __('Step 3 - Configure your items'),
            'class' => 'layout-manager-grid',
        ]);

        $fieldset->addField('layout_configure', 'text', [
            'name' => 'layout_configure',
            'label' => __('Layout configure'),
            'title' => __('Layout configure'),
        ])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Configure'));

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_layout_prepareform',
            ['form' => $form]);

        // Set values to the form
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
