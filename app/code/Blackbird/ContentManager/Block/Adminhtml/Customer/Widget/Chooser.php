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

namespace Blackbird\ContentManager\Block\Adminhtml\Customer\Widget;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Customer\Model\Customer;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Chooser
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Customer\Widget
 */
class Chooser extends Extended
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Website
     */
    protected $_customerWebsite;

    /**
     * @var \Magento\Customer\Model\Config\Source\Group
     */
    protected $_customerGroup;

    /**
     * @var array
     */
    protected $_selectedCustomers = [];

    /**
     * Chooser constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\Config\Source\Group $customerGroup
     * @param \Magento\Config\Model\Config\Source\Website $customerWebsite
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\Config\Source\Group $customerGroup,
        \Magento\Config\Model\Config\Source\Website $customerWebsite,
        array $data = []
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_customerGroup = $customerGroup;
        $this->_customerWebsite = $customerWebsite;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('contentmanager/customer_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            // Load the content
            $collection = $this->_customerCollectionFactory->create();
            $collection->addAttributeToSelect('firstname')
                ->addAttributeToFilter(Customer::CUSTOMER_GRID_INDEXER_ID, (int)$element->getValue());
            $content = ($collection->count()) ? $collection->getFirstItem() : null;

            if ($content && $content->getId()) {
                $chooser->setLabel($this->escapeHtml($content->getTitle()));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        $js = '';

        if ($this->getUseMassaction()) {
            $js = 'function (grid, element) {
                $(grid.containerId).fire("content:changed", {element: element});
            }';

            if ($form = $this->getJsFormObject()) {
                $js = "{$form}.chooserGridCheckboxCheck.bind({$form})";
            }
        }

        return $js;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = '';

        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            $js = '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var contentId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                    var contentTitle = trElement.down("td").next().next().innerHTML;
                    ' . $chooserJsObject . '.setElementValue(contentId);
                    ' . $chooserJsObject . '.setElementLabel(contentTitle);
                    ' . $chooserJsObject . '.close();
                }
            ';
        } else {
            if ($form = $this->getJsFormObject()) {
                $js = "{$form}.chooserGridRowClick.bind({$form})";
            }
        }

        return $js;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('contentmanager/customer_widget/chooser',
            ['_current' => true, 'uniq_id' => $this->getId(), 'use_massaction' => $this->getUseMassaction()]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);

        if ($form = $this->getJsFormObject()) {
            $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_customers') {
            $selected = $this->getSelectedCustomers();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Retrieve the current selected customers
     *
     * @return array
     */
    public function getSelectedCustomers()
    {
        if ($selectedCustomers = $this->getRequest()->getParam('selected', [])) {
            $this->setSelectedCustomers($selectedCustomers);
        }

        return $this->_selectedCustomers;
    }

    /**
     * Set the current selected customers
     *
     * @param array $selectedCustomers
     * @return $this
     */
    public function setSelectedCustomers(array $selectedCustomers)
    {
        $this->_selectedCustomers = $selectedCustomers;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->_customerCollectionFactory->create()->addAttributeToSelect([
            'firstname',
            'lastname',
            'email',
            'created_in',
            'created_at',
        ]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn('in_customer', [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_customers',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_customers',
                'values' => $this->getSelectedCustomers(),
                'align' => 'center',
                'index' => 'entity_id',
                'use_index' => true,
            ]);
        }

        $this->addColumn('chooser_id', [
            'header' => __('ID'),
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);

        $this->addColumn('customer_firstname', [
            'header' => __('Firstname'),
            'index' => 'firstname',
            'header_css_class' => 'col-firstname',
            'column_css_class' => 'col-firstname',
        ]);

        $this->addColumn('customer_lastname', [
            'header' => __('Lastname'),
            'index' => 'lastname',
            'header_css_class' => 'col-lastname',
            'column_css_class' => 'col-lastname',
        ]);

        $this->addColumn('customer_email', [
            'header' => __('Email'),
            'index' => 'email',
            'header_css_class' => 'col-email',
            'column_css_class' => 'col-email',
        ]);

        $this->addColumn('customer_group', [
            'header' => __('Group'),
            'index' => 'group_id',
            'type' => 'options',
            'options' => $this->toOptions($this->_customerGroup->toOptionArray()),
            'header_css_class' => 'col-groupid',
            'column_css_class' => 'col-groupid',
        ]);

        $this->addColumn('customer_website', [
            'header' => __('Website'),
            'index' => 'website_id',
            'type' => 'options',
            'options' => $this->toOptions($this->_customerWebsite->toOptionArray()),
            'header_css_class' => 'col-website',
            'column_css_class' => 'col-website',
        ]);

        $this->addColumn('customer_Account-Created_In', [
            'header' => __('Account Created In'),
            'index' => 'created_in',
            'header_css_class' => 'col-account-created-in',
            'column_css_class' => 'col-account-created-in',
        ]);

        $this->addColumn('customer_since', [
            'header' => __('Customer Since'),
            'index' => 'created_at',
            'header_css_class' => 'col-customer-since',
            'column_css_class' => 'col-customer-since',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Convert an array to options
     *
     * @param array $array
     * @return array
     */
    protected function toOptions(array $array)
    {
        $options = [];

        foreach ($array as $line) {
            $options[$line['value']] = $line['label'];
        }

        return $options;
    }
}