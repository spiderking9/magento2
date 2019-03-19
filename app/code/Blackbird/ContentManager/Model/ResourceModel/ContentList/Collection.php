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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentList;

use Blackbird\ContentManager\Model\ContentList;
use Blackbird\ContentManager\Model\ResourceModel\ContentList as ResourceContentList;
use Magento\Store\Model\Store;

/**
 * Content List Resource Model Collection
 *
 * Class Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\ContentList
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    implements \Blackbird\ContentManager\Api\Data\ContentListInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Collection constructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Add store availability filter. Include availability content for store website
     *
     * @param null|int|array|Store $store
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        $storeIds = [Store::DEFAULT_STORE_ID];

        if ($store === null) {
            $store = $this->_storeManager->getStore();
        }
        if ($store instanceof Store) {
            $store = $store->getId();
        }
        if (is_array($store)) {
            $storeIds = array_merge($store, $storeIds);
        } else {
            $storeIds[] = (int)$store;
        }

        $this->getSelect()
            ->joinLeft(['store_table' => $this->getTable('blackbird_contenttype_list_store')],
                'store_table.cl_id = main_table.cl_id', '')
            ->where('store_table.store_id IN (?)', $storeIds);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ContentList::class, ResourceContentList::class);
        $this->_setIdFieldName(ContentList::ID);
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->_items as $item) {
            $storeIds = $this->getResource()->lookupStoreIds($item->getClId());

            $item->setData('stores', $storeIds);
            $item->setData('flags', $storeIds);
            if (count($storeIds)) {
                $item->setData('_first_store_code', $this->_storeManager->getStore($storeIds[0])->getCode());
            }
        }

        return $this;
    }
}
