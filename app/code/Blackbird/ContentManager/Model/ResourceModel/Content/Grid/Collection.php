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

namespace Blackbird\ContentManager\Model\ResourceModel\Content\Grid;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Magento\Store\Model\Store;

/**
 * Class Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\Content\Grid
 */
class Collection extends \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var bool
     */
    protected $storeFilterFlag = false;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $connection
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreFilter($store = null)
    {
        parent::addStoreFilter($store);
        $this->storeFilterFlag = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $contentTypeModel = $this->_coreRegistry->registry('current_contenttype');

        if ($contentTypeModel) {
            $this->addStoreFilter(Store::DEFAULT_STORE_ID)
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('title')
                ->addAttributeToSelect('status')
                ->addFieldToFilter(ContentType::ID, $contentTypeModel->getCtId());

            $customFieldsCollection = $contentTypeModel->getCustomFieldCollection()
                ->addFieldToFilter('show_in_grid', 1);

            $this->addAttributeToSelect($customFieldsCollection->getColumnValues('identifier'));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeLoad()
    {
        if ($this->storeFilterFlag) {
            $this->joinTable(
                ['entity_store' => $this->getTable('blackbird_contenttype_entity_store')],
                'entity_id=entity_id',
                ['store_id'],
                ['store_id' => $this->getStoreId()]//todo add default store id 0 if null
            );
            $this->groupByAttribute(Content::ID);
        }
        return parent::_beforeLoad();
    }
}
