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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout;

/**
 * Class Layouts
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout
 */
class Layouts extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/layout/layouts.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_layouts;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Layouts constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layouts
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $layouts,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_layouts = $layouts;
        $this->_coreRegistry = $registry;
    }

    /**
     * Render type select field
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTypeSelectHtml()
    {
        /** @var \Magento\Framework\View\Element\Html\Select $select */
        $select = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')->setData([
            'id' => 'contenttype_layout',
            'class' => 'select',
            'label' => __('Content Layout'),
            'title' => __('Content Layout'),
            'note' => __('Select to preview the content layout'),
            'value' => $this->getElement()->getData('value'),
        ])->setName($this->getElement()->getName())->setOptions($this->_layouts->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Get content layout collumns
     *
     * @return string
     */
    public function getContentLayoutColumnsTemplateHtml()
    {
        return $this->getChildHtml('content_layout_columns');
    }

    /**
     * Get content type identifier
     *
     * @return string
     */
    public function getContentTypeIdentifier()
    {
        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $identifier = $contentType->getIdentifier();
        } else {
            $identifier = '';
        }

        return $identifier;
    }

    /**
     * Return list of layout items values
     *
     * @return \Magento\Framework\DataObject
     */
    public function getItemValues()
    {
        $items = [];
        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $collection = null;

        // If we are creating a new content type
        if (!$contentType) {
            return $items;
        }

        // Group items
        $collection = $contentType->getLayoutGroupItemCollection();
        foreach ($collection as $group) {
            $items[$group->getSortOrder()] = new \Magento\Framework\DataObject(array_merge($group->toArray(), [
                'uid' => $group->getId(),
                'parent_id' => $group->getParentLayoutGroupId(),
                'item' => $group->getType(),
            ]));
        }

        // Field items
        $collection = $contentType->getLayoutFieldItemCollection();
        foreach ($collection as $field) {
            $format = unserialize($field->getFormat());

            $items[$field->getSortOrder()] = new \Magento\Framework\DataObject(array_merge($field->toArray(), [
                'uid' => $field->getId(),
                'parent_id' => $field->getLayoutGroupId(),
                'item' => $field->getType(),
                'format' => $format['type'],
                'format_extra' => $format['extra'],
                'format_height' => $format['height'],
                'format_width' => $format['width'],
                'link' => $format['link'],
            ]));
        }

        // Block items
        $collection = $contentType->getLayoutBlockItemCollection();
        foreach ($collection as $block) {
            $items[$block->getSortOrder()] = new \Magento\Framework\DataObject(array_merge($block->toArray(), [
                'uid' => $block->getId(),
                'parent_id' => $block->getLayoutGroupId(),
                'item' => $block->getType(),
            ]));
        }

        ksort($items);

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addChild('content_layout_columns',
            \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Columns::class);

        return $this;
    }
}
