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

namespace Blackbird\ContentManager\Block\Adminhtml\Flag\Edit\Tab;

/**
 * Class Form
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Flag\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\Flags
     */
    protected $_sourceFlags;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory
     */
    protected $_flagCollection;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Flag\Collection
     */
    protected $_flagCollectionInstance;

    /**
     * Form constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\Flags $sourceFlags
     * @param \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Blackbird\ContentManager\Model\Config\Source\Flags $sourceFlags,
        \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_sourceFlags = $sourceFlags;
        $this->_flagCollection = $flagCollection;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Store Flag Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Store Flag Information');
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
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('flag_');

        // General information fieldset
        $fieldset = $form->addFieldset('general_title', ['legend' => __('Assign Flag by Store')]);

        $stores = $this->_storeManager->getStores(true);
        $flags = $this->_sourceFlags->toOptionArray();

        // Sort the stores
        ksort($stores);

        // For each store view
        foreach ($stores as $store) {
            $label = $store->getWebsite()->getName() . ' - ' . $store->getGroup()
                    ->getName() . ' - ' . $store->getName();

            $fieldset->addField('store_' . $store->getId(), 'select', [
                'title' => $store->getName(),
                'label' => $label,
                'name' => 'store_' . $store->getId(),
                'values' => $flags,
                'class' => 'flag_preview',
                'before_element_html' => $this->createBeforeElementHtml($store->getId()),
                'note' => ($store->getId() == 0) ? __('The \'%1\' store render for all store views.', $label) : '',
            ]);
        }

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_flag_general_prepareform', ['form' => $form]);

        $form->setValues($this->getFlagData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return preview image flag
     *
     * @param int $storeId
     * @return string
     */
    protected function createBeforeElementHtml($storeId)
    {
        $html = '<img src="" class="store-flag-icon flag-icon-space" id="flag_store_' . $storeId . '" alt="store-' . $storeId . '" />';

        return $html;
    }

    /**
     * Retrieve data flags
     *
     * @return array
     */
    public function getFlagData()
    {
        $data = [];
        $collection = $this->getFlagCollectionInstance();

        foreach ($collection as $flag) {
            $data['store_' . $flag->getId()] = $flag->getValue();
        }

        return $data;
    }

    /**
     * Retrieve the flag collection instance
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\Flag\Collection
     */
    protected function getFlagCollectionInstance()
    {
        if (!$this->_flagCollectionInstance) {
            $this->_flagCollectionInstance = $this->_flagCollection->create();
        }

        return $this->_flagCollectionInstance;
    }
}
