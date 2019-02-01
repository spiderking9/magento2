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

namespace Blackbird\ContentManager\Block\View;

use Blackbird\ContentManager\Model\ContentType\Layout\Block as LayoutBlock;
use Blackbird\ContentManager\Model\ContentType\Layout\Field as LayoutField;
use Blackbird\ContentManager\Model\ContentType\Layout\Group as LayoutGroup;

/**
 * Class Layout
 *
 * @method \Blackbird\ContentManager\Model\Content getContent()
 * @package Blackbird\ContentManager\Block\View
 */
class Layout extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/view/layout.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_layoutSource;

    /**
     * Layout constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layoutSource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layoutSource,
        array $data = []
    ) {
        $this->_layoutSource = $layoutSource;
        parent::__construct($context, $data);
    }

    /**
     * Display the rendered layout of the content
     *
     * @return string
     */
    public function getLayoutHtml()
    {
        $html = '';
        $layoutTemplate = $this->getData('layout_template');
        $layoutItems = $this->getLayoutItems($this->getContentType());

        foreach ($layoutTemplate['column'] as $column) {
            $float = !empty($column['float']) ? ' ct-float-' . $column['float'] : '';
            $width = !empty($column['width']) ? ' ct-width-' . $column['width'] : '';
            $class = 'ct-column ct-column-' . $column['class'] . $width . $float;
            $id = 'ct-column-' . $column['id'];

            // If there are many items for this column
            if (isset($layoutItems[$column['id']])) {
                $html .= $this->getColumnDivHtml($class, $id, $layoutItems[$column['id']]);
            } else {
                $html .= $this->getColumnDivHtml($class, $id);
            }
        }

        return $html;
    }

    /**
     * Retrieves the layout item collection ordered by column et sort order
     *
     * @param \Blackbird\ContentManager\Api\ContentLayoutInterface $contentLayout
     * @return array
     */
    public function getLayoutItems(\Blackbird\ContentManager\Api\ContentLayoutInterface $contentLayout)
    {
        $items = [];

        // Group items
        $collection = $contentLayout->getLayoutGroupItemCollection()
            ->addFieldToFilter(LayoutGroup::PARENT_ID, ['null' => true]);
        foreach ($collection as $group) {
            $items[$group->getColumn()][$group->getSortOrder()] = $group;
        }

        // Field items
        $collection = $contentLayout->getLayoutFieldItemCollection()
            ->addFieldToFilter(LayoutField::PARENT_ID, ['null' => true]);
        foreach ($collection as $field) {
            $items[$field->getColumn()][$field->getSortOrder()] = $field;
        }

        // Block items
        $collection = $contentLayout->getLayoutBlockItemCollection()
            ->addFieldToFilter(LayoutBlock::PARENT_ID, ['null' => true]);
        foreach ($collection as $block) {
            $items[$block->getColumn()][$block->getSortOrder()] = $block;
        }

        // Sort array in right order
        foreach ($items as $columnKey => $columnItems) {
            ksort($items[$columnKey]);
        }
        ksort($items);

        return $items;
    }

    /**
     * Display data into a 'column' div
     *
     * @param string $class
     * @param string $id
     * @param array $layoutItems
     * @return string
     */
    public function getColumnDivHtml($class = '', $id = '', $layoutItems = [])
    {
        $html = '';
        $class = !empty($class) ? ' class="' . $class . '"' : '';
        $id = !empty($id) ? ' id="' . $id . '"' : '';

        $html .= '<div' . $class . $id . '>';
        $html .= '   <div class="inside">';

        /** @var $layoutItem \Blackbird\ContentManager\Model\ContentType\Layout\AbstractModel */
        foreach ($layoutItems as $layoutItem) {
            $html .= $this->getContent()->render($layoutItem, ['has_link' => $this->getAllHasLink()]);
        }

        $html .= '   </div>';
        $html .= '</div>';

        return $html;
    }
}
