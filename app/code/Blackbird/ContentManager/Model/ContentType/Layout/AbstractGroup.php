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

namespace Blackbird\ContentManager\Model\ContentType\Layout;

use Blackbird\ContentManager\Model\ContentType\Layout\Block as LayoutBlock;
use Blackbird\ContentManager\Model\ContentType\Layout\Field as LayoutField;
use Blackbird\ContentManager\Model\ContentType\Layout\Group as LayoutGroup;

/**
 * Class Group
 *
 * @package Blackbird\ContentManager\Model\ContentType\Layout
 */
abstract class AbstractGroup extends \Blackbird\ContentManager\Model\ContentType\Layout\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentType\Layout\GroupInterface
{
    // Override in child class
    protected $_layoutBlockCollectionFactory = null;
    // Override in child class
    protected $_layoutFieldCollectionFactory = null;

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        // Delete the children
        $this->deleteChildren();

        return $this;
    }

    /**
     * Delete all children
     *
     * @return $this
     */
    protected function deleteChildren()
    {
        foreach ($this->getChildren() as $child) {
            $child->delete();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        if (!$this->hasData('children')) {
            $this->loadChildren();
        }

        return $this->getData('children');
    }

    /**
     * Load and set the children of a group
     *
     * @return $this
     */
    public function loadChildren()
    {
        $children = [];
        $collection = null;

        // Load block children collection
        $collection = $this->_layoutBlockCollectionFactory->create()
            ->addFieldToFilter(LayoutBlock::PARENT_ID, $this->getId())
            ->setOrder(LayoutBlock::SORT_ORDER);
        foreach ($collection as $block) {
            $children[$block->getSortOrder()] = $block;
        }

        // Load field children collection
        $collection = $this->_layoutFieldCollectionFactory->create()
            ->addFieldToFilter(LayoutField::PARENT_ID, $this->getId())
            ->setOrder(LayoutField::SORT_ORDER);
        foreach ($collection as $field) {
            $children[$field->getSortOrder()] = $field;
        }

        // Load group children collection
        $collection = $this->getCollection()
            ->addFieldToFilter(LayoutGroup::PARENT_ID, $this->getId())
            ->setOrder(LayoutGroup::SORT_ORDER);
        foreach ($collection as $group) {
            $children[$group->getSortOrder()] = $group;
        }

        ksort($children);
        $this->setChildren($children);

        return $this;
    }

    /**
     * Set the children
     *
     * @param array $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $this->setData('children', $children);

        return $this;
    }
}
