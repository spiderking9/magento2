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
 * Class Breadcrumb
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Breadcrumb extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentList\Breadcrumb
     */
    protected $_breadcrumbSource;

    /**
     * Breadcrumb constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentList\Breadcrumb $breadcrumbSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentList\Breadcrumb $breadcrumbSource,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_breadcrumbSource = $breadcrumbSource;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Breadcrumbs');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Breadcrumbs');
    }

    /**
     * Can show tab or not
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
    public function _prepareForm()
    {
        $contentList = $this->_coreRegistry->registry('current_contentlist');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contentlist_');

        /** Breadcrumbs */

        $fieldset = $form->addFieldset('breadcrumbs_fieldset', ['legend' => __('Breadcrumbs')]);

        $fieldset->addField(ContentListData::BREADCRUMB, 'select', [
            'name' => ContentListData::BREADCRUMB,
            'label' => __('Last Crumb'),
            'title' => __('Last Crumb'),
            'note' => __('Select the field to use as breadcrumb. You can create a new field dedicated to the breadcrumb name. Save your content in order to see your new fields in this list.'),
            'required' => false,
            'values' => $this->_breadcrumbSource->toOptionArray(),
        ]);

        $fieldset->addField(ContentListData::BREADCRUMB_CUSTOM_TITLE, 'text', [
            'name' => ContentListData::BREADCRUMB_CUSTOM_TITLE,
            'label' => __('Custom Breadcrumb'),
            'title' => __('Custom Breadcrumb'),
            'note' => __('Is required only if \'Custom Breadcrumb\' is matched.'),
        ]);

        /** Breadcrumbs by stores */

        $data = [];
        if ($contentList) {
            $data = $contentList->getData();
        }

        $fieldsetStore = [];
        $stores = $this->_storeManager->getStores();

        // Prepare array of breadcrumbs by store
        if (!empty($data['breadcrumb_prev_name'])) {
            $data['breadcrumb_prev_name'] = unserialize($data['breadcrumb_prev_name']);
        }

        if (!empty($data['breadcrumb_prev_link'])) {
            $data['breadcrumb_prev_link'] = unserialize($data['breadcrumb_prev_link']);
        }

        foreach ($stores as $store) {
            $fieldsetStore[$store->getId()] = $form->addFieldset('breadcrumbs_fieldset_' . $store->getId(), [
                'legend' => __('Middle Breadcrumb - ' . $store->getName() . ' (' . $store->getCode() . ')'),
                'collapsable' => true,
            ]);

            $fieldsetStore[$store->getId()]->addField('breadcrumb_prev_name_' . $store->getId(), 'text', [
                'name' => 'breadcrumb_prev_name[' . $store->getId() . ']',
                'label' => __('N-x Breadcrumb Name'),
                'title' => __('N-x Breadcrumb Name'),
                'note' => __('You can add many middle crumbs. The crumbs should be separate by ";". Keep empty to skip this feature.'),
                'required' => false,
            ]);

            $fieldsetStore[$store->getId()]->addField('breadcrumb_prev_link_' . $store->getId(), 'text', [
                'name' => 'breadcrumb_prev_link[' . $store->getId() . ']',
                'label' => __('N-x Breadcrumb Link'),
                'title' => __('N-x Breadcrumb Link'),
                'note' => __('Type your middle crumbs links. The crumbs should be separate by ";". Keep empty for no link on it.'),
                'required' => false,
            ]);

            /** Init data values */
            if (isset($data['breadcrumb_prev_name'], $data['breadcrumb_prev_name'][$store->getId()])) {
                $data['breadcrumb_prev_name_' . $store->getId()] = $data['breadcrumb_prev_name'][$store->getId()];
            }

            if (isset($data['breadcrumb_prev_link'], $data['breadcrumb_prev_link'][$store->getId()])) {
                $data['breadcrumb_prev_link_' . $store->getId()] = $data['breadcrumb_prev_link'][$store->getId()];
            }
        }

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contentlist_breadcrumb_prepareform',
            ['form' => $form]);

        // Set data values
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
