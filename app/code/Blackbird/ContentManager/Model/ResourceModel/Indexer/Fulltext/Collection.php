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

namespace Blackbird\ContentManager\Model\ResourceModel\Indexer\Fulltext;

use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;

/**
 * Fulltext Collection
 *
 * Class Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\Indexer\Fulltext
 */
class Collection extends \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
{
    /**
     * @var string
     */
    protected $queryText;

    /**
     * @var string|null
     */
    protected $order = null;

    /**
     * @var string
     */
    protected $searchRequestName;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    protected $temporaryStorageFactory;

    /**
     * @var \Magento\Search\Api\SearchInterface
     */
    protected $search;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\Search\SearchResultInterface
     */
    protected $searchResult;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Collection constructor
     *
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
     * @param \Magento\Search\Api\SearchInterface $searchInterface
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param SearchResultFactory $searchResultFactory
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param string $searchRequestName
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
        \Magento\Search\Api\SearchInterface $searchInterface,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\SearchResultFactory $searchResultFactory,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'quick_search_container_contentmanager'
    ) {
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->search = $searchInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultFactory = $searchResultFactory;
        $this->filterBuilder = $filterBuilder;
        $this->searchRequestName = $searchRequestName;
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
     * Add search query filter
     *
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->queryText) {
            $filterBuilder = $this->getFilterBuilder()->create();
            $filterBuilder->setField('search_term');
            $filterBuilder->setValue($this->queryText);
            $this->getSearchCriteriaBuilder()->addFilter($filterBuilder);
        }

        $searchCriteria = $this->getSearchCriteriaBuilder()->create();
        $searchCriteria->setRequestName($this->getSearchRequestName());
        try {
            $this->searchResult = $this->getSearch()->search($searchCriteria);
        } catch (EmptyRequestDataException $e) {
            /** @var \Magento\Framework\Api\Search\SearchResultInterface $searchResult */
            $this->searchResult = $this->searchResultFactory->create()->setItems([]);
        } catch (NonExistingRequestNameException $e) {
            $this->_logger->error($e->getMessage());
            throw new LocalizedException(__('Sorry, something went wrong. You can find out more in the error log.'));
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($this->searchResult->getItems());

        $this->getSelect()->joinInner(
            ['search_result' => $table->getName()],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
            []
        );

        $this->_totalRecords = $this->searchResult->getTotalCount();

        if ($this->order && 'relevance' === $this->order['field']) {
            $this->getSelect()->order('search_result.' . TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Get filter builder
     *
     * @return \Magento\Framework\Api\FilterBuilder
     */
    protected function getFilterBuilder()
    {
        return $this->filterBuilder;
    }

    /**
     * Get search criteria builder
     *
     * @return \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Get search request name
     *
     * @return string
     */
    protected function getSearchRequestName()
    {
        return $this->searchRequestName;
    }

    /**
     * Get search model
     *
     * @return \Magento\Search\Api\SearchInterface
     */
    protected function getSearch()
    {
        return $this->search;
    }
}
