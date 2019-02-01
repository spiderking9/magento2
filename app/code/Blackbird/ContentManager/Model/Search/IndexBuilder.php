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

namespace Blackbird\ContentManager\Model\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Adapter\Mysql\IndexBuilderInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Build base Query for Index
 *
 * Class IndexBuilder
 *
 * @package Blackbird\ContentManager\Model\Search
 */
class IndexBuilder implements IndexBuilderInterface
{
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var IndexScopeResolver
     */
    protected $scopeResolver;

    /**
     * @var ConditionManager
     */
    protected $conditionManager;

    /**
     * @var TableMapper
     */
    protected $tableMapper;

    /**
     * @var ScopeResolverInterface
     */
    protected $dimensionScopeResolver;

    /**
     * IndexBuilder constructor
     *
     * @param ResourceConnection $resource
     * @param ConditionManager $conditionManager
     * @param IndexScopeResolver $scopeResolver
     * @param TableMapper $tableMapper
     * @param ScopeResolverInterface $dimensionScopeResolver
     */
    public function __construct(
        ResourceConnection $resource,
        ConditionManager $conditionManager,
        IndexScopeResolver $scopeResolver,
        TableMapper $tableMapper,
        ScopeResolverInterface $dimensionScopeResolver
    ) {
        $this->resource = $resource;
        $this->conditionManager = $conditionManager;
        $this->scopeResolver = $scopeResolver;
        $this->tableMapper = $tableMapper;
        $this->dimensionScopeResolver = $dimensionScopeResolver;
    }

    /**
     * Build index query
     *
     * @param RequestInterface $request
     * @return Select
     */
    public function build(RequestInterface $request)
    {
        $searchIndexTable = $this->scopeResolver->resolve($request->getIndex(), $request->getDimensions());
        $select = $this->resource->getConnection()
            ->select()
            ->from(['search_index' => $searchIndexTable], ['entity_id' => 'entity_id'])
            ->joinLeft(['cea' => $this->resource->getTableName('blackbird_contenttype_eav_attribute')],
                'search_index.attribute_id = cea.attribute_id', []);

        $select = $this->tableMapper->addTables($select, $request);

        $select = $this->processDimensions($request, $select);

        return $select;
    }

    /**
     * Add filtering by dimensions
     *
     * @param RequestInterface $request
     * @param Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function processDimensions(RequestInterface $request, Select $select)
    {
        $dimensions = $this->prepareDimensions($request->getDimensions());

        $query = $this->conditionManager->combineQueries($dimensions, Select::SQL_OR);
        if (!empty($query)) {
            $select->where($this->conditionManager->wrapBrackets($query));
        }

        return $select;
    }

    /**
     * Prepares where conditions from dimensions
     *
     * @param Dimension[] $dimensions
     * @return string[]
     */
    protected function prepareDimensions(array $dimensions)
    {
        $preparedDimensions = [];
        foreach ($dimensions as $dimension) {
            if ('scope' === $dimension->getName()) {
                continue;
            }
            $preparedDimensions[] = $this->conditionManager->generateCondition($dimension->getName(), '=',
                $this->dimensionScopeResolver->getScope($dimension->getValue())->getId());
        }

        return $preparedDimensions;
    }
}
