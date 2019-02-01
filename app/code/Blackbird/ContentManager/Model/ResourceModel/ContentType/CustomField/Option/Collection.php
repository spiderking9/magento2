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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option;

use Blackbird\ContentManager\Model\ContentType\CustomField\Option;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option as ResourceOption;

/**
 * Custom Field Resource Model Collection
 *
 * Class Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Add 'title' value
     *
     * @todo default title and title by store
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option\Collection
     */
    public function addTitleToResult()
    {
        $table = $this->getTable('blackbird_contenttype_option_type_title');

        $this->getSelect()
            ->join(['opt_title' => $table], 'opt_title.option_type_id = main_table.option_type_id', 'title')
            ->where('opt_title.store_id = ?', \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Option::class, ResourceOption::class);
    }
}
