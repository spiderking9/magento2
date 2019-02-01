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
use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;

/**
 * Class Config
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Config extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentTypes
     */
    protected $_contentTypeSource;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesnoConfig;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $_customFieldsSource;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentList\PagerPosition
     */
    protected $_pagerPositionConfig;

    /**
     * Config constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypesSource
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldSource
     * @param \Magento\Config\Model\Config\Source\Yesno $yesnoConfig
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentList\PagerPosition $pagerPositionConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypesSource,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldSource,
        \Magento\Config\Model\Config\Source\Yesno $yesnoConfig,
        \Blackbird\ContentManager\Model\Config\Source\ContentList\PagerPosition $pagerPositionConfig,
        array $data = []
    ) {
        $this->_contentTypeSource = $contentTypesSource;
        $this->_yesnoConfig = $yesnoConfig;
        $this->_customFieldsSource = $customFieldSource;
        $this->_pagerPositionConfig = $pagerPositionConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Options');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Options');
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

        /** Options */
        $fieldset = $form->addFieldset('options_fieldset', ['legend' => __('Content List Options')]);

        $fieldset->addField(ContentListData::CT_ID, 'select', [
            'name' => ContentListData::CT_ID,
            'label' => __('Content Type'),
            'title' => __('Content Type'),
            'required' => true,
            'options' => $this->_contentTypeSource->getOptions(['neq' => Visibility::REPEATER_FIELD]),
        ]);

        $fieldset->addField(ContentListData::PAGER, 'select', [
            'name' => ContentListData::PAGER,
            'label' => __('Display Page Control'),
            'title' => __('Display Page Control'),
            'required' => true,
            'values' => $this->_yesnoConfig->toOptionArray(),
        ]);

        $fieldset->addField(ContentListData::PAGER_POSITION, 'select', [
            'name' => ContentListData::PAGER_POSITION,
            'label' => __('Pager Position'),
            'title' => __('Pager Position'),
            'required' => true,
            'values' => $this->_pagerPositionConfig->toOptionArray(),
        ]);

        $fieldset->addField(ContentListData::LIMIT_PAGE, 'text', [
            'name' => ContentListData::LIMIT_PAGE,
            'label' => __('Number of Contents per Page'),
            'title' => __('Number of Contents per Page'),
            'required' => true,
        ]);

        $fieldset->addField(ContentListData::LIMIT, 'text', [
            'name' => ContentListData::LIMIT,
            'label' => __('Number of Contents to Display'),
            'title' => __('Number of Contents to Display'),
            'required' => true,
        ]);

        $fieldset->addField(ContentListData::ORDER_FIELD, 'select', [
            'name' => ContentListData::ORDER_FIELD,
            'label' => __('Order Field'),
            'title' => __('Order Field'),
            'note' => __('Select the sort order attribute.'),
            'required' => true,
            'values' => $this->_customFieldsSource->toOptionArray(),
        ]);

        $fieldset->addField(ContentListData::SORT_ORDER, 'select', [
            'name' => ContentListData::SORT_ORDER,
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'required' => true,
            'values' => [
                ['value' => 'ASC', 'label' => __('Ascending')],
                ['value' => 'DESC', 'label' => __('Descending')],
            ],
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contentlist_config_prepareform',
            ['form' => $form]);

        // Default values
        $data = [
            ContentListData::LIMIT_PAGE => '5',
            ContentListData::LIMIT => '10',
        ];

        $contentList = $this->_coreRegistry->registry('current_contentlist');
        if ($contentList) {
            $data = array_merge($data, $contentList->getData());
        }
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
