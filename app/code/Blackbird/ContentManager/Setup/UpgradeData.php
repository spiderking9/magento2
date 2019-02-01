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

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentList;
use Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory as ContentCollectionFactory;
use Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory as ContentListCollectionFactory;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory as CustomFieldCollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Upgrade Data script
 *
 * Class UpgradeData
 *
 * @package Blackbird\ContentManager\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * @var CustomFieldCollectionFactory
     */
    private $_customFieldCollectionFactory;

    /**
     * @var ContentCollectionFactory
     */
    private $_contentCollectionFactory;

    /**
     * @var ContentListCollectionFactory
     */
    private $_contentListCollectionFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * UpgradeData constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\FieldDataConverterFactory $fieldDataConverterFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        FieldDataConverterFactory $fieldDataConverterFactory,
        CustomFieldCollectionFactory $customFieldCollectionFactory,
        ContentCollectionFactory $contentCollectionFactory,
        ContentListCollectionFactory $contentListCollectionFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_storeManager = $storeManager;
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->_customFieldCollectionFactory = $customFieldCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
    }

    /**
     * Upgrade Data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Update all content and content lists urls
        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            try {
                $this->upgradeUrlRewrite();
            } catch (LocalizedException $e) {
                //@todo refactor
                //@hack Is a fresh installation with no existing contents
            }
        }
        if (version_compare($context->getVersion(), '2.1.19', '<')) {
            $this->upgradeSerializedFields($setup);
        }
        if (version_compare($context->getVersion(), '2.1.23', '<')) {
            $this->fixIntegerFieldType($setup);
        }
        if (version_compare($context->getVersion(), '2.1.24', '<')) {
            $this->addContentImportAttribute($setup);
        }

        //todo add attributes for meta canonical

        $setup->endSetup();
    }

    /**
     * Upgrade data for the url rewrite implementation
     *
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    private function upgradeUrlRewrite()
    {
        foreach ($this->_storeManager->getStores() as $store) {
            $this->updateContentUrls($store->getId());
            $this->updateContentListUrls($store->getId());
        }
    }

    /**
     * Update all urls of the enabled contents
     *
     * @param $storeId
     * @return void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    private function updateContentUrls($storeId)
    {
        $contentCollection = $this->_contentCollectionFactory->create()->addStoreFilter($storeId)->addIsVisibleFilter();

        foreach ($contentCollection as $content) {
            try {
                $content->generateUrls();
            } catch (AlreadyExistsException $e) {
                throw new AlreadyExistsException($this->getContentUrlAlreadyExistsMessage($content, $storeId));
            }
        }
    }

    /**
     * Get Content URL Already Exists Message
     *
     * @param Content $content
     * @param $storeId
     * @return \Magento\Framework\Phrase
     */
    private function getContentUrlAlreadyExistsMessage(Content $content, $storeId)
    {
        return __('Content ID: \'%1\' for Store ID: \'%2\' has an already used URL: \'%3\'. Modify the url before continue upgrading.',
            $content->getId(), $storeId, $content->getUrlKey());
    }

    /**
     * Update all urls of the enabled content list
     *
     * @param $storeId
     * @return void
     * @throws AlreadyExistsException
     */
    private function updateContentListUrls($storeId)
    {
        $contentListCollection = $this->_contentListCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addFieldToFilter(ContentList::STATUS, 1);

        foreach ($contentListCollection as $contentList) {
            try {
                $contentList->generateUrls();
            } catch (AlreadyExistsException $e) {
                throw new AlreadyExistsException($this->getContentListUrlAlreadyExistsMessage($contentList, $storeId));
            }
        }
    }

    /**
     * Get Content List URL Already Exists Message
     *
     * @param ContentList $contentList
     * @param $storeId
     * @return \Magento\Framework\Phrase
     */
    private function getContentListUrlAlreadyExistsMessage(ContentList $contentList, $storeId)
    {
        return __('ContentList ID: \'%1\' for Store ID: \'%2\' has an already used URL: \'%3\'. Modify the url before continue upgrading.',
            $contentList->getId(), $storeId, $contentList->getUrlKey());
    }

    /**
     * Convert serialized data in fields to json format
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     * @throws \Magento\Framework\DB\FieldDataConversionException
     */
    private function upgradeSerializedFields(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(SerializedToJson::class);

        $fieldDataConverter->convert($setup->getConnection(), $setup->getTable('blackbird_contenttype_list'), 'cl_id',
            'conditions');
    }

    /**
     * Fix the custom field type 'integer'
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function fixIntegerFieldType(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $attributeIds = $this->_customFieldCollectionFactory->create()
            ->addFieldToSelect(['attribute_id'])
            ->addFieldToFilter('type', 'integer')
            ->getColumnValues('attribute_id');

        // Update the EAV attributes backend type
        $connection->update($setup->getTable('eav_attribute'), ['backend_type' => 'int'],
            ['attribute_id IN (?)' => $attributeIds]);

        // Move the attributes data to the right place
        $duplicate = $connection->insertFromSelect($connection->select()
            ->from($setup->getTable('blackbird_contenttype_entity_text'),
                ['attribute_id', 'store_id', 'entity_id', 'value'])
            ->where('attribute_id IN (?)', $attributeIds), $setup->getTable('blackbird_contenttype_entity_int'),
            ['attribute_id', 'store_id', 'entity_id', 'value']);
        $connection->query($duplicate);

        // Delete the old attributes data saved in the wrong place
        $connection->delete($setup->getTable('blackbird_contenttype_entity_text'),
            ['attribute_id IN (?)' => $attributeIds]);
    }

    /**
     * Add the new import attribute to the content eav
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @return void
     */
    private function addContentImportAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(\Blackbird\ContentManager\Model\Content::ENTITY, 'import_identifier', [
            'type' => 'varchar',
            'label' => 'Import Identifier',
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => true,
            'filterable' => true,
            'comparable' => false,
            'is_global' => true,
            'global' => \Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
        ]);
    }
}
