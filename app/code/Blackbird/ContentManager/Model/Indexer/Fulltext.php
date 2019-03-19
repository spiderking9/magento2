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

namespace Blackbird\ContentManager\Model\Indexer;

use Blackbird\ContentManager\Model\Indexer\Fulltext\Action\FullFactory;
use Blackbird\ContentManager\Model\ResourceModel\Indexer\Fulltext as FulltextResource;
use Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory;
use Magento\Framework\Search\Request\Config as SearchRequestConfig;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Fulltext
 *
 * @package Blackbird\ContentManager\Model\Indexer
 */
class Fulltext implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'blackbird_contenttype_fulltext';

    /**
     * @var \Blackbird\ContentManager\Model\Indexer\Fulltext\Action\Full
     */
    protected $_fullAction;

    /**
     * @var \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory
     */
    protected $_indexerHandlerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DimensionFactory
     */
    protected $_dimensionFactory;

    /**
     * @var FulltextResource
     */
    protected $_fulltextResource;

    /**
     * @var SearchRequestConfig
     */
    protected $_searchRequestConfig;

    /**
     * @var array index structure
     */
    protected $data;

    /**
     * @param \Blackbird\ContentManager\Model\Indexer\Fulltext\Action\FullFactory $fullActionFactory
     * @param \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory $indexerHandlerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Indexer\Fulltext $fulltextResource
     * @param \Magento\Framework\Search\Request\Config $searchRequestConfig
     * @param array $data
     */
    public function __construct(
        FullFactory $fullActionFactory,
        IndexerHandlerFactory $indexerHandlerFactory,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        FulltextResource $fulltextResource,
        SearchRequestConfig $searchRequestConfig,
        array $data
    ) {
        $this->_fullAction = $fullActionFactory->create(['data' => $data]);
        $this->_indexerHandlerFactory = $indexerHandlerFactory;
        $this->_storeManager = $storeManager;
        $this->_dimensionFactory = $dimensionFactory;
        $this->_fulltextResource = $fulltextResource;
        $this->_searchRequestConfig = $searchRequestConfig;
        $this->data = $data;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->execute();
        $this->_fulltextResource->resetSearchResults();
        $this->_searchRequestConfig->reset();
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids = null)
    {
        $storeIds = array_keys($this->_storeManager->getStores());
        $saveHandler = $this->createIndexerHandler();

        foreach ($storeIds as $storeId) {
            $dimension = $this->_dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $saveHandler->eraseIndex([$dimension], $ids);
            $saveHandler->saveIndex([$dimension], $this->_fullAction->rebuildStoreIndex($storeId, $ids));
        }
    }

    /**
     * Create indexer handler
     *
     * @return \Magento\Framework\Indexer\SaveHandler\IndexerInterface
     */
    protected function createIndexerHandler()
    {
        return $this->_indexerHandlerFactory->create(['data' => $this->data]);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }
}
