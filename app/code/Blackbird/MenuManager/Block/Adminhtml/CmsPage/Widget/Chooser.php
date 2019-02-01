<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */

namespace Blackbird\MenuManager\Block\Adminhtml\CmsPage\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Chooser extends \Blackbird\MenuManager\Block\Adminhtml\Widget\Chooser\AbstractChooser
{

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * @var
     */
    protected $_enabledisable;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $_pageLayoutBuilder;

    /**
     * Chooser constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param \Magento\Cms\Model\Page $cmsPage
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->_pageCollectionFactory = $pageCollectionFactory;
        $this->_enabledisable = $enabledisable;
        $this->_cmsPage = $cmsPage;
        $this->_pageFactory = $pageFactory;
        $this->_pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('page_id');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);

        if ($form = $this->getJsFormObject()) {
            $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        }
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'menumanager/cmspage_widget/chooser',
            ['uniq_id' => $uniqId]
        );

        $chooser = $this->getLayout()->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Chooser'
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            // Load the collection
            $collection = $this->_pageCollectionFactory->create();
            $collection->addFieldToSelect('identifier');
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_pages') {
            $selected = $this->getSelectedEntities();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('identifier', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('identifier', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }



    /**
     * Prepare content collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_pageCollectionFactory->create();
        $collection
            ->addFieldToSelect('identifier')
            ->addFieldToSelect('title')
            ->addFieldToSelect('page_id')
            ->addFieldToSelect('page_layout')
            ->addFieldToSelect('is_active');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
            $this->addColumn(
                'in_pages',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_pages',
                    'inline_css' => 'checkbox entities',
                    'field_name' => 'in_pages',
                    'values' => $this->getSelectedEntities(),
                    'align' => 'center',
                    'index' => 'identifier',
                    'use_index' => true
                ]
            );

        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'index' => 'page_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'chooser_title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title'
            ]
        );

        $this->addColumn(
            'chooser_identifier',
            [
                'header' => __('URL Key'),
                'index' => 'identifier',
                'header_css_class' => 'col-identifier',
                'column_css_class' => 'col-identifier'
            ]
        );

        $this->addColumn(
            'chooser_page_layout',
            [
                'header' => __('Layout'),
                'index' => 'page_layout',
                'type' => 'options',
                'options' => $this->_pageLayoutBuilder->getPageLayoutsConfig()->getOptions(),
                'header_css_class' => 'col-layout',
                'column_css_class' => 'col-layout'
            ]
        );
        $this->addColumn(
            'chooser_is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->_cmsPage->getAvailableStatuses(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'menumanager/cmspage_widget/chooser',
            [
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction(),
            ]
        );
    }
}