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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout;

/**
 * Class Layouts
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab\Layout
 */
class Layouts extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Layouts
{
    /**
     * Default template
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contentlist/edit/tab/layout/layouts.phtml';

    /**
     * Retrieve the content type identifier of the current content list
     *
     * @return string
     */
    public function getContentTypeIdentifier()
    {
        $contentList = $this->_coreRegistry->registry('current_contentlist');
        $identifier = ($contentList) ? $contentList->getContentType()->getIdentifier() : '';

        return $identifier;
    }

    /**
     * Return list of layout items values
     *
     * @return \Magento\Framework\DataObject|array
     */
    public function getItemValues()
    {
        $items = [];
        $contentList = $this->_coreRegistry->registry('current_contentlist');;
        $collection = null;

        // If we are creating a new content type
        if (!$contentList) {
            return $items;
        }

        // Group items
        $collection = $contentList->getLayoutGroupItemCollection();
        foreach ($collection as $group) {
            $items[$group->getSortOrder()] = new \Magento\Framework\DataObject(array_merge($group->toArray(), [
                'uid' => $group->getId(),
                'parent_id' => $group->getParentLayoutGroupId(),
                'item' => $group->getType(),
            ]));
        }

        // Field items
        $collection = $contentList->getLayoutFieldItemCollection();
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
        $collection = $contentList->getLayoutBlockItemCollection();
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
}
