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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget;

use Blackbird\ContentManager\Model\Content;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Chooser
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget
 */
class Chooser extends Extended
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentTypes
     */
    protected $_contentTypesSource;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enabledisable;

    /**
     * List of selected content
     *
     * @var array
     */
    protected $_selectedContents = [];

    /**
     * Chooser constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypesSource
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypesSource,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_contentTypesSource = $contentTypesSource;
        $this->_enabledisable = $enabledisable;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('contentmanager/content_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            // Load the content
            $collection = $this->_contentCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addAttributeToFilter(Content::ID, (int)$element->getValue());

            if ($collection->count()) {
                $chooser->setLabel($this->escapeHtml($collection->getFirstItem()->getTitle()));
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
        return $this->getUrl('contentmanager/content_widget/chooser', [
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
        ]);
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
        /** @noinspection PhpParamsInspection */
        $this->setDefaultFilter(['chooser_is_active' => '1']);

        if ($form = $this->getJsFormObject()) {
            $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        }
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_contents') {
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $this->getSelectedContents()]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $this->getSelectedContents()]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Getter of selected contents from the list
     *
     * @return array
     */
    public function getSelectedContents()
    {
        if ($selectedContents = $this->getRequest()->getParam('selected', [])) {
            $this->setSelectedContents($selectedContents);
        }

        return $this->_selectedContents;
    }

    /**
     * Setter of selected contents from the list
     *
     * @param array $selectedContents
     * @return $this
     */
    public function setSelectedContents(array $selectedContents)
    {
        $this->_selectedContents = $selectedContents;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->_contentCollectionFactory->create()
            ->addStoreFilter()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('status');

        // Filter by the content type of the content field
        if (!empty($this->getCtIdentifier())) {
            $collection->addContentTypeFilter($this->getCtIdentifier());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn('in_contents', [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_contents',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_contents',
                'values' => $this->getSelectedContents(),
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

        $this->addColumn('chooser_flag', [
            'header' => __('Flag'),
            'index' => '',
            'header_css_class' => 'col-flag',
            'column_css_class' => 'col-flag',
            'renderer' => 'Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer\Flag',
            'sortable' => false,
            'filter' => false,
        ]);

        $this->addColumn('chooser_title', [
            'header' => __('Title'),
            'index' => 'title',
            'header_css_class' => 'col-title',
            'column_css_class' => 'col-title',
        ]);

        $this->addColumn('chooser_identifier', [
            'header' => __('URL Key'),
            'index' => 'url_key',
            'header_css_class' => 'col-url',
            'column_css_class' => 'col-url',
        ]);

        $this->addColumn('chooser_content_type', [
            'header' => __('Content Type'),
            'index' => 'ct_id',
            'type' => 'options',
            'options' => $this->_contentTypesSource->getOptions(),
            'header_css_class' => 'col-layout',
            'column_css_class' => 'col-layout',
        ]);

        $this->addColumn('chooser_is_active', [
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => $this->toOptions($this->_enabledisable->toOptionArray()),
            'header_css_class' => 'col-status',
            'column_css_class' => 'col-status',
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
