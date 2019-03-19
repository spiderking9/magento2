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
 * Class Sitemap
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Sitemap extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @var \Magento\Sitemap\Model\Config\Source\Frequency
     */
    protected $_frequency;

    /**
     * Sitemap constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magento\Sitemap\Model\Config\Source\Frequency $frequency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Sitemap\Model\Config\Source\Frequency $frequency,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        $this->_frequency = $frequency;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Google Sitemap');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Google Sitemap');
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
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        $fieldset = $form->addFieldset('sitemap_fieldset', ['legend' => __('Sitemap')]);

        $fieldset->addField('sitemap_enable', 'select', [
            'name' => 'sitemap_enable',
            'label' => __('Enabled'),
            'title' => __('Enabled'),
            'required' => false,
            'note' => __('Make sure the Google Sitemap is activated. (System > Configuration > Catalog > Google Sitemap)'),
            'values' => $this->_yesno->toOptionArray(),
        ]);

        $fieldset->addField('sitemap_frequency', 'select', [
            'name' => 'sitemap_frequency',
            'label' => __('Frequency'),
            'title' => __('Frequency'),
            'required' => true,
            'values' => $this->_frequency->toOptionArray(),
        ]);

        $fieldset->addField('sitemap_priority', 'text', [
            'name' => 'sitemap_priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'class' => 'validate-digits-range digits-range-0-1',
            'note' => __('Valid values range: from 0.0 to 1.0.'),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_sitemap_prepareform',
            ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
