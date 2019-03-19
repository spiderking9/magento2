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

namespace Blackbird\ContentManager\Model\Indexer\Fulltext\Action;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\Indexer\Fulltext;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Full
 *
 * @package Blackbird\ContentManager\Model\Indexer\Fulltext\Action
 */
class Full
{
    /**
     * Scope identifier
     */
    const SCOPE_FIELD_NAME = 'scope';

    /**
     * Index values separator
     *
     * @var string
     */
    const SEPARATOR = ' | ';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Search\Request\Config
     */
    protected $_searchRequestConfig;

    /**
     * @var \Magento\Framework\Indexer\SaveHandler\IndexerInterface
     */
    protected $_indexHandler;

    /**
     * @var \Magento\Framework\Search\Request\DimensionFactory
     */
    protected $_dimensionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_contentTypeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Collection
     */
    protected $_searchableContentTypes = null;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\Collection
     */
    protected $_searchableAttributes = null;

    /**
     * @var array
     */
    protected $_searchableContents = null;

    /**
     * Full constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Search\Request\Config $searchRequestConfig
     * @param \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory $indexHandlerFactory
     * @param \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory
     * @param \Magento\Framework\Indexer\ConfigInterface $indexerConfig
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Search\Request\Config $searchRequestConfig,
        \Blackbird\ContentManager\Model\Indexer\IndexerHandlerFactory $indexHandlerFactory,
        \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory,
        \Magento\Framework\Indexer\ConfigInterface $indexerConfig,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_searchRequestConfig = $searchRequestConfig;
        $configData = $indexerConfig->getIndexer(Fulltext::INDEXER_ID);
        $this->_indexHandler = $indexHandlerFactory->create(['data' => $configData]);
        $this->_dimensionFactory = $dimensionFactory;
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Rebuild whole fulltext index for all stores
     *
     * @return void
     */
    public function reindexAll()
    {
        $storeIds = array_keys($this->_storeManager->getStores());
        foreach ($storeIds as $storeId) {
            $this->cleanIndex($storeId);
            $this->rebuildStoreIndex($storeId);
        }
        $this->_searchRequestConfig->reset();
    }

    /**
     * Clean search index data for store
     *
     * @param int $storeId
     * @return void
     */
    protected function cleanIndex($storeId)
    {
        $dimension = $this->_dimensionFactory->create(['name' => self::SCOPE_FIELD_NAME, 'value' => $storeId]);
        $this->_indexHandler->cleanIndex([$dimension]);
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId
     * @param array $contentIds
     * @return \Generator
     */
    public function rebuildStoreIndex($storeId, $contentIds = null)
    {
        $contents = $this->getSearchableContents($storeId, $contentIds);
        $attributes = $this->getSearchableAttributes();

        foreach ($contents as $entityId => $content) {
            yield $entityId => $this->prepareContentIndex($attributes, $content, $storeId);
        }
    }

    /**
     * Get the searchable contents
     *
     * @param int $storeId
     * @param array|int $contentIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getSearchableContents($storeId, $contentIds = null)
    {
        if ($this->_searchableContents === null) {
            $this->_searchableContents = [];
        }

        if (!array_key_exists($storeId, $this->_searchableContents)) {
            $contentTypesIds = $this->getSearchableContentTypes()->getAllIds();
            $contentCollection = [];
            if (!empty($contentTypesIds)) {
                $contentCollection = $this->_contentCollectionFactory->create()
                    ->addStoreFilter($storeId)
                    ->addAttributeToSelect($this->getSearchableAttributeCodes())
                    ->addIsVisibleFilter()
                    ->addAttributeToFilter(Content::CT_ID, $contentTypesIds);
            }
            $this->_searchableContents[$storeId] = $contentCollection;
        }

        $contentCollection = $this->_searchableContents[$storeId];

        if (!empty($contentCollection) && $contentIds !== null) {
            $contentCollection->addAttributeToFilter(Content::ID, $contentIds);
        }

        return $contentCollection;
    }

    /**
     * Retrieve the searchable Content Types
     *
     * @param array|int $contentTypeIds
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Collection
     */
    protected function getSearchableContentTypes($contentTypeIds = null)
    {
        if ($this->_searchableContentTypes === null) {
            $this->_searchableContentTypes = $this->_contentTypeCollectionFactory->create()
                ->addFieldToFilter(ContentType::VISIBILITY, Visibility::VISIBLE)
                ->addFieldToFilter(ContentType::SEARCH_ENABLED, 1);
        }

        $contentTypeCollection = $this->_searchableContentTypes;

        if (!empty($contentTypeIds)) {
            $contentTypeCollection->addFieldToFilter(ContentType::ID, $contentTypeIds);
        }

        return $contentTypeCollection;
    }

    /**
     * Retrieve all searchable attribute codes
     *
     * @return array
     */
    protected function getSearchableAttributeCodes()
    {
        $attributeCodes = [];

        foreach ($this->getSearchableAttributes() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCOde();
        }

        return $attributeCodes;
    }

    /**
     * Retrieve all searchable attributes
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\Collection
     */
    protected function getSearchableAttributes()
    {
        if ($this->_searchableAttributes === null) {
            $this->_searchableAttributes = $this->_attributeCollectionFactory->create()
                ->addFieldToFilter('is_searchable', 1);
        }

        return $this->_searchableAttributes;
    }

    /**
     * Prepare Fulltext index value for content
     *
     * @param AbstractCollection $attributes
     * @param Content $content
     * @return array
     */
    protected function prepareContentIndex(AbstractCollection $attributes, Content $content)
    {
        $index = [];

        foreach ($attributes as $attribute) {
            if ($content->hasData($attribute->getAttributeCode())) {
                $index[$attribute->getAttributeId()] = $content->getData($attribute->getAttributeCode());
            }
        }

        return $index;
    }
}
