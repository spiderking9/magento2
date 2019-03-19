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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the ContentManager module DB scheme
 *
 * Class UpgradeSchema
 *
 * @package Blackbird\ContentManager\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->renameSearchAttributeWeight($setup);
        }
        if (version_compare($context->getVersion(), '2.1.23', '<')) {
            $this->addVisibilityField($setup);
        }
        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->addDefaultImportIdentifierField($setup);
        }

        //todo add fields for meta canonical (content & content list)

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    private function renameSearchAttributeWeight(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->changeColumn($setup->getTable('blackbird_contenttype_eav_attribute'), 'search_attribute_weight',
                'search_weight', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'comment' => 'Attribute search weight',
                ]);
    }

    /**
     * Rename search attribute weight
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addVisibilityField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('blackbird_contenttype'), 'visibility', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 11,
            'comment' => 'Content Type Visibility',
            'default' => \Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility::VISIBLE,
            'nullable' => false,
        ]);
    }

    /**
     * Rename search attribute weight
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addDefaultImportIdentifierField(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable('blackbird_contenttype');
        $connection->addColumn($table, 'default_import_identifier_name', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 10,
            'comment' => 'Default Import Identifier Name',
            'nullable' => false,
        ]);

        $connection->addColumn($table, 'default_import_identifier_value', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 10,
            'comment' => 'Default Import Identifier Value',
            'nullable' => false,
        ]);
    }
}
