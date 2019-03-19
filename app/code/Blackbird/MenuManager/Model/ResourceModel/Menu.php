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
namespace Blackbird\MenuManager\Model\ResourceModel;

use Magento\Eav\Model\Entity\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Menu extends AbstractDb
{
    /**
     * @var string
     */
    protected $_storeMenuTable = '';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected function _construct()
    {
        $this->_init('blackbird_menumanager_menu', 'menu_id');
    }

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                $connectionName = null)
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }


    public function getStoreMenuTable()
    {
        if(empty($this->_storeMenuTable)){
            $this->_storeMenuTable = $this->getTable('blackbird_menumanager_store');
        }

        return $this->_storeMenuTable;
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $menu
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $menu)
    {
        $menuId = $menu->getId();
        // Load menu stores and status
        if ($menuId) {
            $stores = $this->lookupStoreIds($menuId);
            $status = $this->getCurrentStatus($menuId);

            $menu->setData('stores', $stores);
            $menu->setData('menu_status', $status);
        }

        return parent::_afterLoad($menu);
    }

    /**
     * Get the status of the menu given in parameter
     *
     * @param $menuId
     */
    public function getCurrentStatus($menuId)
    {
        $menuId = (int) $menuId;

       $select = $this->getConnection()->select()
            ->from($this->getMainTable(),'is_active')
           ->where('menu_id' . ' = ?', $menuId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get store id to which specified item is assigned
     *
     * @param int $menuListId
     * @return array
     */
    public function lookupStoreIds($menuId)
    {
        $menuId = (int) $menuId;

        $select = $this->getConnection()->select()
            ->from($this->getStoreMenuTable(), 'store_id')
            ->where('menu_id' . ' = ?', $menuId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param $menuIdentifier
     * @return array
     */
    public function lookupStoreIdsByIdentifier($menuIdentifier)
    {
        $tableMenu = $this->getTable('blackbird_menumanager_menu');

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getStoreMenuTable()] , 'store_id')
            ->join(
                ['menu_table' => $tableMenu],
                'menu_table.menu_id = main_table.menu_id',
                '*'
                )
            ->where('menu_table.identifier' . ' = ?', $menuIdentifier);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $menu
     */
    protected function _updateStores(\Magento\Framework\Model\AbstractModel $menu)
    {
        $oldStores = $this->lookupStoreIds($menu->getId());
        $newStores = $menu->getStores();

        $toInsert = array_diff($newStores, $oldStores);
        $toDelete = array_diff($oldStores, $newStores);


        $menuId = $menu->getId();
        $menuTable = $this->getStoreMenuTable();

        // Delete case
        if (!empty($toDelete)) {
            $where = [
                'menu_id' . ' = ?' => $menuId,
                'store_id' . ' IN (?)' => $toDelete
            ];
            $this->getConnection()->delete($menuTable, $where);
        }
        // Insert case
        if (!empty($toInsert)) {
            $toInsert = $this->associatedValues($toInsert, $menuId, 'store_id', 'menu_id');
            $this->getConnection()->insertMultiple($menuTable, $toInsert);
        }
    }

    /**
     * Save relation menu_id/store_id
     *
     * @param \Magento\Framework\DataObject $contentList
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $menu)
    {
        // Update the stores
        $this->_updateStores($menu);

        return parent::_afterSave($menu);
    }

    protected function associatedValues($array, $value, $key1, $key2)
    {
        $return  = [];

        foreach ($array as $val) {
            $return[] = [$key1 => $val, $key2 => $value];
        }

        return $return;
    }

    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $menu)
    {
        $where = ['menu_id' . ' = (?)' => (int) $menu->getId()];

        $this->getConnection()->delete($this->getStoreMenuTable(), $where);

        return parent::_beforeDelete($menu);
    }
}
