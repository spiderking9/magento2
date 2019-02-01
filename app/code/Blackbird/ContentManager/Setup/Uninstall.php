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

use Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 *
 * @package Blackbird\ContentManager\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var CollectionFactory
     */
    private $_contentTypeCollectionFactory;

    /**
     * Content setup factory
     *
     * @var ContentSetupFactory
     */
    private $_contentSetupFactory;

    /**
     * Uninstall constructor
     *
     * @param CollectionFactory $contentTypeCollectionFactory
     * @param ContentSetupFactory $contentSetupFactory
     */
    public function __construct(
        CollectionFactory $contentTypeCollectionFactory,
        ContentSetupFactory $contentSetupFactory
    ) {
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->_contentSetupFactory = $contentSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $uninstaller = $setup;

        $uninstaller->startSetup();

        /** @var ContentSetup $contentSetup */
        $contentSetup = $this->_contentSetupFactory->create();

        // Delete Data
        $contentTypeCollection = $this->_contentTypeCollectionFactory->create();
        foreach ($contentTypeCollection as $contentType) {
            $contentType->delete();
        }

        // Delete the ContentManager entity type
        $contentSetup->removeEntityType(\Blackbird\ContentManager\Model\Content::ENTITY);

        // Delete tables
        foreach ($this->getContentManagerTableNames() as $tableName) {
            $uninstaller->getConnection()->dropTable($uninstaller->getTable($tableName));
        }

        $uninstaller->endSetup();
    }

    /**
     * Retrieve the ACM table names
     *
     * @return array
     */
    private function getContentManagerTableNames()
    {
        return [
            'blackbird_contenttype',
            'blackbird_contenttype_eav_attribute',
            'blackbird_contenttype_eav_attribute_website',
            'blackbird_contenttype_entity',
            'blackbird_contenttype_entity_datetime',
            'blackbird_contenttype_decimal',
            'blackbird_contenttype_int',
            'blackbird_contenttype_store',
            'blackbird_contenttype_text',
            'blackbird_contenttype_varchar',
            'blackbird_contenttype_fieldset',
            'blackbird_contenttype_flag',
            'blackbird_contenttype_layout_block',
            'blackbird_contenttype_layout_field',
            'blackbird_contenttype_layout_group',
            'blackbird_contenttype_layout_review',
            'blackbird_contenttype_list',
            'blackbird_contenttype_list_layout_block',
            'blackbird_contenttype_list_layout_field',
            'blackbird_contenttype_list_layout_group',
            'blackbird_contenttype_list_store',
            'blackbird_contenttype_option',
            'blackbird_contenttype_option_title',
            'blackbird_contenttype_option_type_title',
            'blackbird_contenttype_option_type_value',
            'blackbird_contenttype_review',
        ];
    }
}
