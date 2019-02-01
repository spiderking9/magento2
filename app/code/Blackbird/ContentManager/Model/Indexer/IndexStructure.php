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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;

/**
 * Class IndexStructure
 *
 * @package Blackbird\ContentManager\Model\Indexer
 */
class IndexStructure implements IndexStructureInterface
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * IndexStructure constructor
     *
     * @param ResourceConnection $resource
     * @param IndexScopeResolver $indexScopeResolver
     */
    public function __construct(
        ResourceConnection $resource,
        IndexScopeResolver $indexScopeResolver
    ) {
        $this->resource = $resource;
        $this->indexScopeResolver = $indexScopeResolver;
    }

    /**
     * Delete indexer table
     *
     * @param string $index
     * @param Dimension[] $dimensions
     * @return void
     */
    public function delete($index, array $dimensions = [])
    {
        $tableName = $this->getIndexerTableName($index, $dimensions);

        if ($this->getConnection()->isTableExists($tableName)) {
            $this->getConnection()->dropTable($tableName);
        }
    }

    /**
     * Get indexer table name
     *
     * @param string $index
     * @param array $dimensions
     * @return string
     */
    private function getIndexerTableName($index, array $dimensions = [])
    {
        return $this->indexScopeResolver->resolve($index, $dimensions);
    }

    /**
     * Get connection object
     *
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }

    /**
     * Create indexer table
     *
     * @param string $index
     * @param array $fields
     * @param array $dimensions
     * @throws \Zend_Db_Exception
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $this->createFulltextIndex($this->getIndexerTableName($index, $dimensions));
    }

    /**
     * Create the indexer table
     *
     * @param string $tableName
     * @throws \Zend_Db_Exception
     */
    private function createFulltextIndex($tableName)
    {
        $table = $this->getConnection()->newTable($tableName);

        /** Add Columns */

        $table->addColumn('entity_id', Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable' => false], 'Entity ID')
            ->addColumn('attribute_id', Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable' => false])
            ->addColumn('data_index', Table::TYPE_TEXT, '4g', ['nullable' => true], 'Data index');

        /** Add Indexes */

        $table->addIndex('idx_primary', ['entity_id', 'attribute_id'], ['type' => AdapterInterface::INDEX_TYPE_PRIMARY])
            ->addIndex('FTI_FULLTEXT_DATA_INDEX', ['data_index'], ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]);

        // Create the table
        $this->getConnection()->createTable($table);
    }
}
