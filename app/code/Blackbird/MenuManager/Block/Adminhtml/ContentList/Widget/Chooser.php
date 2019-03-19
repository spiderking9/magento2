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

namespace Blackbird\MenuManager\Block\Adminhtml\ContentList\Widget;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Blackbird\ContentManager\Model\ContentList;

class Chooser extends \Blackbird\MenuManager\Block\Adminhtml\Widget\Chooser\AbstractChooser
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentTypes
     */
    protected $_contentTypesSource;

    /**
     * @var
     */
    protected $_enabledisable;

    /**
     * Chooser constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
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
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory|mixed
     */
    protected function getContentCollectionFactory()
    {
        if(!$this->_contentListCollectionFactory) {
            $this->_contentListCollectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory::class);
        }
        return $this->_contentListCollectionFactory;
    }

    /**
     * @return \Blackbird\ContentManager\Model\Config\Source\ContentTypes|mixed
     */
    protected  function getContentTypeSource()
    {
        if(!$this->_contentTypesSource) {
            $this->_contentTypesSource = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Blackbird\ContentManager\Model\Config\Source\ContentTypes::class);
        }
        return $this->_contentTypesSource;
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
            'menumanager/contentlist_widget/chooser',
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
            // Load the contentlist
            $collection = $this->getContentCollectionFactory()->create();
            $collection->addAttributeToSelect('title')
                ->addAttributeToFilter(ContentList::ID, (int)$element->getValue());
            $content = ($collection->getSize() > 0) ? $collection->getFirstItem(): null;

            if ($content && $content->getId()) {
                $chooser->setLabel($this->escapeHtml($content->getTitle()));
            }
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
        if ($column->getId() == 'in_contentlists') {
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
     * Prepare content collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getContentCollectionFactory()->create();

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
        if ($this->getUseMassaction()) {
            $this->addColumn(
                'in_contentlists',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_contentlists',
                    'inline_css' => 'checkbox entities',
                    'field_name' => 'in_contentlists',
                    'values' => $this->getSelectedEntities(),
                    'align' => 'center',
                    'index' => 'cl_id',
                    'use_index' => true
                ]
            );
        }

        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'index' => 'cl_id',
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
                'index' => 'url_key',
                'header_css_class' => 'col-url',
                'column_css_class' => 'col-url'
            ]
        );

        $this->addColumn(
            'chooser_content_type',
            [
                'header' => __('Content Type'),
                'index' => 'ct_id',
                'type' => 'options',
                'options' => $this->toOptions($this->getContentTypeSource()->toOptionArray()),
                'header_css_class' => 'col-layout',
                'column_css_class' => 'col-layout'
            ]
        );

        $this->addColumn(
            'chooser_is_active',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->toOptions($this->_enabledisable->toOptionArray()),
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
            'menumanager/contentlist_widget/chooser',
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
}