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

namespace Blackbird\ContentManager\Model\Indexer\Fulltext;

use Blackbird\ContentManager\Model\Indexer\Fulltext as FulltextIndexer;

/**
 * Class Store
 *
 * @package Blackbird\ContentManager\Model\Indexer\Fulltext
 */
class Store implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Search\Request\DimensionFactory
     */
    protected $dimensionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory
     */
    protected $indexerHandlerFactory;

    /**
     * @var \Magento\Framework\Indexer\ConfigInterface
     */
    protected $indexerConfig;

    /**
     * Store constructor
     *
     * @param \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory
     * @param \Magento\Framework\Indexer\ConfigInterface $indexerConfig
     * @param \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory $indexerHandlerFactory
     */
    public function __construct(
        \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory,
        \Magento\Framework\Indexer\ConfigInterface $indexerConfig,
        \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory $indexerHandlerFactory
    ) {
        $this->dimensionFactory = $dimensionFactory;
        $this->indexerHandlerFactory = $indexerHandlerFactory;
        $this->indexerConfig = $indexerConfig;
    }

    /**
     * Execute indexer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $observer->getEvent()->getData('store');
        $this->clearIndex($store);
    }

    /**
     * Clear index
     *
     * @param \Magento\Store\Model\Store $store
     * @return void
     */
    protected function clearIndex(\Magento\Store\Model\Store $store)
    {
        $dimensions = [
            $this->dimensionFactory->create(['name' => 'scope', 'value' => $store->getId()]),
        ];
        $configData = $this->indexerConfig->getIndexer(FulltextIndexer::INDEXER_ID);
        /** @var \Blackbird\ContentManager\Model\Indexer\IndexerHandler $indexHandler */
        $indexHandler = $this->indexerHandlerFactory->create(['data' => $configData]);
        $indexHandler->cleanIndex($dimensions);
    }
}
