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

namespace Blackbird\ContentManager\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Blackbird\ContentManager\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;

        $installer->startSetup();


        /**
         * Create table 'blackbird_contenttype'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype'))
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], null)
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('breadcrumb', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('breadcrumb_prev_link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [], null)
            ->addColumn('breadcrumb_prev_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [], null)
            ->addColumn('created_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
            ], null)
            ->addColumn('update_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
            ], null)
            ->addColumn('default_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('page_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('meta_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('meta_robots', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('og_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('og_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('og_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('og_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('og_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [
                'nullable' => false,
            ], null)
            ->addColumn('reviews_enabled', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('reviews_default_status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
            ], null)
            ->addColumn('search_enabled', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], null)
            ->addColumn('layout', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
            ], null)
            ->addColumn('root_template', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 100, [], null)
            ->addColumn('layout_update_xml', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [], null)
            ->addColumn('sitemap_enable', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '3',
            ], null)
            ->addColumn('sitemap_frequency', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => '0',
                'nullable' => false,
            ], null)
            ->addColumn('sitemap_priority', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 10, [
                'default' => '0',
                'nullable' => false,
            ], null)
            ->addColumn('default_status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '3',
            ], null)
            ->setComment('blackbird_contenttype');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_eav_attribute'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_eav_attribute'))
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
                'primary' => true,
            ], 'Attribute ID')
            ->addColumn('is_global', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '1',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Is Global')
            ->addColumn('is_searchable', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], null)
            ->addColumn('search_attribute_weight', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '1',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], null)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_eav_attribute', 'attribute_id',
                'eav_attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT EAV Attribute Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity'))
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Entity ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Content Type ID')
            ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
            ], 'Creation Time')
            ->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
            ], 'Update Time')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity', 'ct_id', 'blackbird_contenttype',
                'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity_datetime'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_datetime'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Value ID')
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Entity ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Value')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_datetime',
                ['attribute_id', 'store_id', 'entity_id'],
                \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                ['attribute_id', 'store_id', 'entity_id'],
                ['type' => \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_datetime', ['attribute_id']),
                ['attribute_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_datetime', ['store_id']), ['store_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_datetime', ['entity_id']), ['entity_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_datetime', 'attribute_id',
                'eav_attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_datetime', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_datetime', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Datetime Attribute Backend Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'blackbird_contenttype_entity_decimal'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_decimal'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Value ID')
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Entity ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, [12, 4], [
                'scale' => '4',
                'precision' => '12',
            ], 'Value')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_decimal',
                ['attribute_id', 'store_id', 'entity_id'],
                \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                ['attribute_id', 'store_id', 'entity_id'],
                ['type' => \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_decimal', ['attribute_id']),
                ['attribute_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_decimal', ['store_id']), ['store_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_decimal', ['entity_id']), ['entity_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_decimal', 'attribute_id',
                'eav_attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_decimal', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_decimal', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Decimal Attribute Backend Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity_int'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_int'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Value ID')
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Entity ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Value')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_int',
                ['attribute_id', 'store_id', 'entity_id'],
                \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                ['attribute_id', 'store_id', 'entity_id'],
                ['type' => \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_int', ['attribute_id']), ['attribute_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_int', ['store_id']), ['store_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_int', ['entity_id']), ['entity_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_int', 'attribute_id', 'eav_attribute',
                'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_int', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_int', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Integer Attribute Backend Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_store'))
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'primary' => true,
            ], 'Content ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
                'primary' => true,
            ], 'Store ID')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_store', ['store_id']), ['store_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_store', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_store', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Content Entity To Store Linkage Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity_text'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_text'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Value ID')
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Entity ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 16000, [], 'Value')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_text',
                ['attribute_id', 'store_id', 'entity_id'],
                \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                ['attribute_id', 'store_id', 'entity_id'],
                ['type' => \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_text', ['attribute_id']), ['attribute_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_text', ['store_id']), ['store_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_text', ['entity_id']), ['entity_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_text', 'attribute_id', 'eav_attribute',
                'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_text', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_text', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Text Attribute Backend Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_entity_varchar'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_entity_varchar'))
            ->addColumn('value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Value ID')
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Entity ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Value')
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_varchar',
                ['attribute_id', 'store_id', 'entity_id'],
                \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
                ['attribute_id', 'store_id', 'entity_id'],
                ['type' => \Magento\Framework\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_varchar', ['attribute_id']),
                ['attribute_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_varchar', ['store_id']), ['store_id'])
            ->addIndex($installer->getIdxName('blackbird_contenttype_entity_varchar', ['entity_id']), ['entity_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_varchar', 'attribute_id',
                'eav_attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_varchar', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_entity_varchar', 'entity_id',
                'blackbird_contenttype_entity', 'entity_id'), 'entity_id',
                $installer->getTable('blackbird_contenttype_entity'), 'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Dynamic Content Varchar Attribute Backend Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_fieldset'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_fieldset'))
            ->addColumn('fieldset_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Fieldset ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Content type ID')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Type')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Sort Order')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_fieldset', 'ct_id', 'blackbird_contenttype',
                'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Fieldset Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_flag'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_flag'))
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => false,
                'primary' => true,
            ], 'Store ID')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Type')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_flag', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Flags Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_layout_block'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_layout_block'))
            ->addColumn('layout_block_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout Block ID')
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Layout Group ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Content Type ID')
            ->addColumn('block_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'HTML element type')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element class')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'default' => '0',
                'precision' => '5',
            ], 'Show Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Sort Order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Label HTML element type')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_block', 'layout_group_id',
                'blackbird_contenttype_layout_group', 'layout_group_id'), 'layout_group_id',
                $installer->getTable('blackbird_contenttype_layout_group'), 'layout_group_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_block', 'ct_id',
                'blackbird_contenttype', 'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_block', 'block_id', 'cms_block',
                'block_id'), 'block_id', $installer->getTable('cms_block'), 'block_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Layout Block Association Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_layout_field'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_layout_field'))
            ->addColumn('layout_field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout Field ID')
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Layout Group ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Content Type ID')
            ->addColumn('custom_field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'precision' => '10',
                'unsigned' => true,
            ], null)
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'HTML element type')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element class')
            ->addColumn('format', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Data formating')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'default' => '0',
                'precision' => '5',
            ], 'Show Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Sort Order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Label HTML element type')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_field', 'layout_group_id',
                'blackbird_contenttype_layout_group', 'layout_group_id'), 'layout_group_id',
                $installer->getTable('blackbird_contenttype_layout_group'), 'layout_group_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_field', 'ct_id',
                'blackbird_contenttype', 'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_field', 'custom_field_id',
                'blackbird_contenttype_option', 'option_id'), 'custom_field_id',
                $installer->getTable('blackbird_contenttype_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Layout Field Association Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_layout_group'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_layout_group'))
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout Group ID')
            ->addColumn('parent_layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Parent Layout Group ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Content Type ID')
            ->addColumn('html_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element name')
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'HTML element type')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'HTML element class')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'default' => '0',
                'precision' => '5',
            ], 'Show Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'precision' => '10',
            ], 'Sort Order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Label HTML element type')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_group', 'parent_layout_group_id',
                'blackbird_contenttype_layout_group', 'layout_group_id'), 'parent_layout_group_id',
                $installer->getTable('blackbird_contenttype_layout_group'), 'layout_group_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_layout_group', 'ct_id',
                'blackbird_contenttype', 'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('CT Layout Group Association Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_option'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_option'))
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Option ID')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Cct ID')
            ->addColumn('type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [], 'Type')
            ->addColumn('is_require', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'default' => '1',
                'nullable' => false,
                'precision' => '5',
            ], 'Is Required')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Sort Order')
            ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], null)
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'precision' => '5',
                'unsigned' => true,
            ], null)
            ->addColumn('fieldset_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], null)
            ->addColumn('show_in_grid', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('note', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('default_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('max_characters', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
            ], null)
            ->addColumn('wysiwyg_editor', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('crop', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('crop_w', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
            ], null)
            ->addColumn('crop_h', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
            ], null)
            ->addColumn('keep_aspect_ratio', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addColumn('file_path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('img_alt', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], null)
            ->addColumn('img_title', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], null)
            ->addColumn('img_url', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], null)
            ->addColumn('file_extension', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('content_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addColumn('attribute', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], null)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option', 'ct_id', 'blackbird_contenttype',
                'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option', 'attribute_id', 'eav_attribute',
                'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option', 'fieldset_id',
                'blackbird_contenttype_fieldset', 'fieldset_id'), 'fieldset_id',
                $installer->getTable('blackbird_contenttype_fieldset'), 'fieldset_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Option Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_option_title'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_option_title'))
            ->addColumn('option_title_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Option Title ID')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Page Title')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option_title', 'option_id',
                'blackbird_contenttype_option', 'option_id'), 'option_id',
                $installer->getTable('blackbird_contenttype_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option_title', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Option Title Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_option_type_value'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_option_type_value'))
            ->addColumn('option_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Option Type ID')
            ->addColumn('option_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Option ID')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Sort Order')
            ->addColumn('value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'default' => '0',
            ], null)
            ->addColumn('default', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
            ], null)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option_type_value', 'option_id',
                'blackbird_contenttype_option', 'option_id'), 'option_id',
                $installer->getTable('blackbird_contenttype_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Option Type Value Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_option_type_title'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_option_type_title'))
            ->addColumn('option_type_title_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Option Type Title ID')
            ->addColumn('option_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'default' => '0',
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Option Type ID')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'default' => '0',
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store ID')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Title')
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option_type_title', 'option_type_id',
                'blackbird_contenttype_option_type_value', 'option_type_id'), 'option_type_id',
                $installer->getTable('blackbird_contenttype_option_type_value'), 'option_type_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_option_type_title', 'store_id', 'store',
                'store_id'), 'store_id', $installer->getTable('store'), 'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Type Option Type Title Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_eav_attribute_website'
         *
         * @todo: feature
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_eav_attribute_website'))
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ], 'Attribute Id')
            ->addColumn('website_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ], 'Website Id')
            ->addColumn('is_visible', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
                'unsigned' => true,
            ], 'Is Visible')
            ->addColumn('is_required', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
                'unsigned' => true,
            ], 'Is Required')
            ->addColumn('default_value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Default Value')
            ->addColumn('multiline_count', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [
                'unsigned' => true,
            ], 'Multiline Count')
            ->addIndex($installer->getIdxName('blackbird_contenttype_eav_attribute_website', ['website_id']),
                ['website_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_eav_attribute_website', 'attribute_id',
                'eav_attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_eav_attribute_website', 'website_id',
                'store_website', 'website_id'), 'website_id', $installer->getTable('store_website'), 'website_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Content Eav Attribute Website');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'blackbird_contenttype_list'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_list'))
            ->addColumn('cl_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Cl_id')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Title')
            ->addColumn('url_key', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Url_key')
            ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'nullable' => false,
            ], 'Status')
            ->addColumn('text_before', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [
                'nullable' => false,
            ], 'Text_before')
            ->addColumn('text_after', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [
                'nullable' => false,
            ], 'Text_after')
            ->addColumn('ct_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Ct_id')
            ->addColumn('limit_per_page', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Limit_per_page')
            ->addColumn('limit_display', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Limit_display')
            ->addColumn('order_field', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Order_field')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 15, [
                'nullable' => false,
            ], 'Sort_order')
            ->addColumn('pager', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'nullable' => false,
            ], 'Define if the page is enabled or disabled')
            ->addColumn('pager_position', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'nullable' => false,
                'precision' => '5',
            ], 'Pagination')
            ->addColumn('conditions', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '16M', [
                'nullable' => true,
            ], 'Serialized conditions for the content filter')
            ->addColumn('breadcrumb', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Breadcrumb')
            ->addColumn('breadcrumb_custom_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [],
                'Breadcrumb_custom_title')
            ->addColumn('breadcrumb_prev_link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [],
                'Breadcrumb_prev_link')
            ->addColumn('breadcrumb_prev_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [],
                'Breadcrumb_prev_name')
            ->addColumn('meta_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Meta_title')
            ->addColumn('meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable' => false,
            ], 'Meta_description')
            ->addColumn('meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable' => false,
            ], 'Meta_keywords')
            ->addColumn('meta_robots', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Meta_robots')
            ->addColumn('og_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Og_title')
            ->addColumn('og_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Og_url')
            ->addColumn('og_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable' => false,
            ], 'Og_description')
            ->addColumn('og_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Og_image')
            ->addColumn('og_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable' => false,
            ], 'Og_type')
            ->addColumn('layout', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'nullable' => false,
                'precision' => '10',
            ], 'Layout')
            ->addColumn('root_template', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 100, [
                'nullable' => false,
            ], 'Root_template')
            ->addColumn('layout_update_xml', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable' => false,
            ], 'Layout_update_xml')
            ->addIndex($installer->getIdxName('blackbird_contenttype_list', ['ct_id']), ['ct_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list', 'ct_id', 'blackbird_contenttype',
                'ct_id'), 'ct_id', $installer->getTable('blackbird_contenttype'), 'ct_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('blackbird_contenttype_list');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_list_layout_block'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_list_layout_block'))
            ->addColumn('layout_block_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout_block_id')
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Layout_group_id')
            ->addColumn('cl_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Cl_id')
            ->addColumn('block_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'nullable' => false,
                'precision' => '5',
            ], 'Block_id')
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [], 'Html_tag')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_class')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], 'Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Sort_order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Html_label_tag')
            ->addIndex($installer->getIdxName('blackbird_contenttype_list_layout_block', ['cl_id']), ['cl_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_layout_block', 'cl_id',
                'blackbird_contenttype_list', 'cl_id'), 'cl_id', $installer->getTable('blackbird_contenttype_list'),
                'cl_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('blackbird_contenttype_list_layout_block');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_list_layout_field'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_list_layout_field'))
            ->addColumn('layout_field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout_field_id')
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Layout_group_id')
            ->addColumn('cl_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Cl_id')
            ->addColumn('custom_field_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'precision' => '10',
                'unsigned' => true,
            ], null)
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [], 'Html_tag')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_class')
            ->addColumn('format', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Format')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], 'Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Sort_order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Html_label_tag')
            ->addIndex($installer->getIdxName('blackbird_contenttype_list_layout_field', ['cl_id']), ['cl_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_layout_field', 'cl_id',
                'blackbird_contenttype_list', 'cl_id'), 'cl_id', $installer->getTable('blackbird_contenttype_list'),
                'cl_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_layout_field', 'custom_field_id',
                'blackbird_contenttype_option', 'option_id'), 'custom_field_id',
                $installer->getTable('blackbird_contenttype_option'), 'option_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('blackbird_contenttype_list_layout_field');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_list_layout_group'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_list_layout_group'))
            ->addColumn('layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
                'auto_increment' => true,
                'primary' => true,
            ], 'Layout_group_id')
            ->addColumn('parent_layout_group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Parent_layout_group_id')
            ->addColumn('cl_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Cl_id')
            ->addColumn('html_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_name')
            ->addColumn('html_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [], 'Html_tag')
            ->addColumn('html_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_id')
            ->addColumn('html_class', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Html_class')
            ->addColumn('label', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'precision' => '5',
            ], 'Label')
            ->addColumn('column', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Column')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                'precision' => '10',
            ], 'Sort_order')
            ->addColumn('html_label_tag', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                'default' => 'div',
            ], 'Html_label_tag')
            ->addIndex($installer->getIdxName('blackbird_contenttype_list_layout_group', ['cl_id']), ['cl_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_layout_group', 'cl_id',
                'blackbird_contenttype_list', 'cl_id'), 'cl_id', $installer->getTable('blackbird_contenttype_list'),
                'cl_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('blackbird_contenttype_list_layout_group');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'blackbird_contenttype_list_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blackbird_contenttype_list_store'))
            ->addColumn('cl_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'nullable' => false,
                'precision' => '10',
                'unsigned' => true,
            ], 'Cl_id')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'nullable' => false,
                'precision' => '5',
                'unsigned' => true,
            ], 'Store_id')
            ->addIndex($installer->getIdxName('blackbird_contenttype_list_store', ['store_id']), ['store_id'])
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_store', 'cl_id',
                'blackbird_contenttype_list', 'cl_id'), 'cl_id', $installer->getTable('blackbird_contenttype_list'),
                'cl_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('blackbird_contenttype_list_store', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('blackbird_contenttype_list_store');
        $installer->getConnection()->createTable($table);


        $installer->endSetup();

    }
}
