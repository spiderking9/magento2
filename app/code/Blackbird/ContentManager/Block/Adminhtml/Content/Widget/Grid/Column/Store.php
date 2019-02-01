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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column;

use Blackbird\ContentManager\Model\ResourceModel\Content\Grid\Collection;
use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Class Store
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column
 */
class Store extends Column
{
    /**
     * Callback
     *
     * @return array
     */
    public function getFilterConditionCallback()
    {
        return [$this, 'addStoreFilter'];
    }

    /**
     * Add store to filter
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\Grid\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    public function addStoreFilter(Collection $collection, Column $column)
    {
        $collection->addStoreFilter($column->getFilter()->getEscapedValue());

        return $this;
    }
}
