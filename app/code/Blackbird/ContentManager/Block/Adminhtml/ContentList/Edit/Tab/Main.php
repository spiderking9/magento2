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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * Class Main
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

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
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
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
        return __('Content List Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Content List Information');
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
        $form->setHtmlIdPrefix('contentlist_');

        /** Informations */

        $fieldset = $form->addFieldset('informations_fieldset', ['legend' => __('Content List Information')]);

        $fieldset->addField(ContentListData::TITLE, 'text', [
            'name' => ContentListData::TITLE,
            'label' => __('Page Title'),
            'title' => __('Page Title'),
            'required' => true,
        ]);

        $fieldset->addField(ContentListData::URL_KEY, 'text', [
            'name' => ContentListData::URL_KEY,
            'label' => __('URL Key'),
            'title' => __('URL Key'),
            'class' => 'validate-identifier',
            'note' => __('Relative to Web Site Base URL'),
        ]);

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('stores', 'multiselect', [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            ])->setRenderer($this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'));
        } else {
            $fieldset->addField('stores', 'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]);
        }

        $fieldset->addField(ContentListData::STATUS, 'select', [
            'name' => ContentListData::STATUS,
            'label' => __('Status'),
            'title' => __('Status'),
            'required' => true,
            'values' => $this->_enabledisable->toOptionArray(),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contentlist_informations_prepareform',
            ['form' => $form]);

        $contentList = $this->_coreRegistry->registry('current_contentlist');
        if ($contentList) {
            if ($this->_storeManager->isSingleStoreMode()) {
                $contentList->setData('stores', $this->_storeManager->getStore(true)->getId());
            }
            $form->setValues($contentList->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
