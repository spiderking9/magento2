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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates;

/**
 * Class Columns
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates
 */
class Columns extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type\AbstractType
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/layout/templates/columns.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_layouts;

    /**
     * Columns constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layouts
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layouts,
        array $data = []
    ) {
        $this->_layouts = $layouts;
        parent::__construct($context, $data);
    }

    /**
     * Renders layouts templates
     *
     * @return string
     */
    public function getLayoutsTemplatesHtml()
    {
        $html = '';

        foreach ($this->getLayoutsArray() as $layouts) {
            $html .= $this->buildLayoutTemplatesHtml($layouts['layout']);
        }

        return $html;
    }

    /**
     * Get layouts list
     *
     * @return array
     */
    public function getLayoutsArray()
    {
        return $this->_layouts->toArray();
    }

    /**
     * Build layout templates
     *
     * @param array $dataLayout
     * @return string
     */
    public function buildLayoutTemplatesHtml(array $dataLayout)
    {
        $html = '';

        foreach ($dataLayout as $layout) {
            if (is_array($layout)) {
                $class = 'layout_columns';
                $id = 'layout_' . $layout['id'];
                $insetHtml = $this->buildColumnsHtml($layout['column']);

                // Create layout template
                $html .= '<script id="contenttype-content-layout-columns-' . $layout['id'] . '" type="text/x-magento-template">' . PHP_EOL;
                $html .= $this->buildDivHtml($layout['id'], $class, $id, $insetHtml);
                $html .= '</script>';
            }
        }

        return $html;
    }

    /**
     * Build columns
     *
     * @param array $dataColumn
     * @return string
     */
    public function buildColumnsHtml(array $dataColumn)
    {
        $html = '';

        foreach ($dataColumn as $column) {
            if (is_array($column)) {
                $width = !empty($column['width']) ? ' column-' . $column['width'] : '';
                $float = !empty($column['float']) ? ' f-' . $column['float'] : '';
                $class = 'column-dropable column column-' . $column['class'] . $width . $float;
                $id = 'col' . $column['id'];
                $html .= $this->buildDivHtml($column['id'], $class, $id);
            }
        }

        return $html;
    }

    /**
     * Build html div elements
     *
     * @param int $id
     * @param string $htmlClass
     * @param string $htmlId
     * @param string $insetHtml
     * @return string
     */
    public function buildDivHtml($id, $htmlClass = '', $htmlId = '', $insetHtml = '')
    {
        $html = '<div class="' . $htmlClass . '" id="' . $htmlId . '" data-id="' . $id . '">';
        if (!empty($insetHtml)) {
            $html .= PHP_EOL . $insetHtml;
        }
        $html .= '</div>' . PHP_EOL;

        return $html;
    }
}
