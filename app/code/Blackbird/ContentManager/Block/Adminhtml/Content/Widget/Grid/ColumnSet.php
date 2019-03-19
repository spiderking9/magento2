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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid;

/**
 * Class ColumnSet
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid
 */
class ColumnSet extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Path to template file in theme
     *
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/grid/column_set.phtml';

    /**
     * Path to template file in theme
     *
     * @var string
     */
    protected $_newColumnsAdded = false;

    /**
     * ColumnSet constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory
     * @param \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals
     * @param \Magento\Backend\Model\Widget\Grid\Totals $totals
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory,
        \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals,
        \Magento\Backend\Model\Widget\Grid\Totals $totals,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $generatorFactory, $subtotals, $totals, $data);
    }

    /**
     * Retrieve the list of columns
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getColumns()
    {
        // Add dynamic columns based on current content type
        if (!$this->_newColumnsAdded) {
            $contentTypeModel = $this->_coreRegistry->registry('current_contenttype');
            $customFieldsCollection = $contentTypeModel->getCustomFieldCollection()
                ->addFieldToFilter('show_in_grid', 1);

            foreach ($customFieldsCollection as $field) {
                $newColumn = $this->getLayout()
                    ->createBlock(\Magento\Backend\Block\Widget\Grid\Column::class, $field->getIdentifier())
                    ->setHeader(__($field->getTitle()))
                    ->setIndex($field->getIdentifier());

                // Render Custom Field Image
                if ($field->getType() === 'image') {
                    $newColumn->setSortable(false);
                    $newColumn->setData('filter', false);
                    $renderer = $this->getLayout()
                        ->createBlock(\Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer\Image::class);
                    $renderer->setColumn($newColumn);
                    $newColumn->setRenderer($renderer);
                }

                $this->append($newColumn);
            }

            $this->_newColumnsAdded = true;
        }

        // Loop columns default + dynamic
        $columns = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        $columnsToTheEnd = [];

        foreach ($columns as $key => $column) {
            if (in_array($key, $this->getLastColumns())) {
                $columnsToTheEnd[$key] = $column;
                unset($columns[$key]);
            }
            if (!$column->isDisplayed()) {
                unset($columns[$key]);
            }
        }

        foreach ($columnsToTheEnd as $key => $column) {
            $columns[$key] = $column;
        }

        return $columns;
    }

    /**
     * Get last collumns of the grid
     *
     * @return array
     */
    protected function getLastColumns()
    {
        return [
            'action',
            'updated_at',
            'created_at',
            'store_view',
            'status',
        ];
    }
}
