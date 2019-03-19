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
namespace Blackbird\MenuManager\Block\Adminhtml\Category\Widget;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Chooser extends \Blackbird\MenuManager\Block\Adminhtml\Widget\Chooser\AbstractChooser
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $_categoryTypesSource;

    /**
     * @var
     */
    protected $_enabledisable;

    /**
     * Chooser constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\Config\Source\Category $categoryTypesSource
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\Config\Source\Category $categoryTypesSource,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_categoryTypesSource = $categoryTypesSource;
        $this->_enabledisable = $enabledisable;
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
        $this->setDefaultSort('title');
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
            'menumanager/category_widget/chooser',
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
        if ($column->getId() == 'in_categories') {
            $selected = $this->getSelectedEntities();
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
     * Prepare category collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_categoryCollectionFactory->create()->addAttributeToSelect("*");

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
                'in_categories',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_categories',
                    'inline_css' => 'checkbox entities',
                    'field_name' => 'in_categories',
                    'values' => $this->getSelectedEntities(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'use_index' => true
                ]
            );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'categoryName',
            [
                'header' => __('Category Name'),
                'index' => 'name',
                'header_css_class' => 'col-categoryName',
                'column_css_class' => 'col-categoryName'
            ]
        );
        $this->addColumn(
          'categoryParent',
            [
                'header' => __('Parent Category'),
                'index' => 'parent_id',
                'header_css_class' => 'col-categoryParent',
                'column_css_class' => 'col-categoryParent'
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
            'menumanager/category_widget/chooser',
            [
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction(),
            ]
        );
    }

    /**
     * Convet an array to options
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

    public function getMultipleRows($item)
    {
        return [];
    }
}
