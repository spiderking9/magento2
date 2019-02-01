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
 * Class Search
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Search extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * Search constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Search');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Search');
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
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        $fieldset = $form->addFieldset('search_fieldset', ['legend' => __('Search')]);

        $fieldset->addField('search_enabled', 'select', [
            'name' => 'search_enabled',
            'label' => __('Enabled'),
            'title' => __('Enabled'),
            'required' => false,
            'note' => __('Set to yes if you want your content type searchable in Frontend default Magento search functionality.<br>Enable the search before editing the search options on the custom fields.'),
            'values' => $this->_yesno->toOptionArray(),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_search_prepareform',
            ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
