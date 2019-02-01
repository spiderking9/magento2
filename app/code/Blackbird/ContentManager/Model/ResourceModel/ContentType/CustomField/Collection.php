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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField;

use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField as ResourceCustomField;

/**
 * Custom Field Resource Model Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Add 'title' value
     *
     * @todo default title and title by store
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Collection
     */
    public function addTitleToResult()
    {
        $table = $this->getTable('blackbird_contenttype_option_title');

        $this->getSelect()
            ->join(['opt_title' => $table], 'opt_title.option_id = main_table.option_id', 'title')
            ->where('opt_title.store_id = ?', \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add content type 'title'
     *
     * @return $this
     */
    public function addContentTypeTitleToResult()
    {
        $table = $this->getTable('blackbird_contenttype');

        $this->getSelect()
            ->join(['ct_entity' => $table], 'ct_entity.ct_id = main_table.ct_id',
                ['content_type_title' => 'ct_entity.title']);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CustomField::class, ResourceCustomField::class);
    }
}
