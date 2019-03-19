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
namespace Blackbird\MenuManager\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Blackbird\MenuManager\Model\Menu', 'Blackbird\MenuManager\Model\ResourceModel\Menu');
    }

    /**
     * Add a store filter on the collection of menu
     *
     * @param $identifier
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($identifier, $storeId)
    {
        $tableStoreMenu = $this->getTable('blackbird_menumanager_store');
        return $this->join(
                ['store_table' => $tableStoreMenu],
                'main_table.menu_id = store_table.menu_id',
                '*')
            ->addFilter('main_table.identifier', $identifier)
            ->addFilter('main_table.is_active', 1)
            ->addFilter('store_table.store_id', $storeId);
    }
}
