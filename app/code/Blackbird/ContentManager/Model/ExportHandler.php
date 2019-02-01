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

namespace Blackbird\ContentManager\Model;


use Blackbird\ContentManager\Model\ContentType\CustomField;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportHandler
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\CsvFactory
     */
    protected $csvFactory;

    /**
     * @var ContentType
     */
    protected $contentTypeModel;

    /**
     * @var ResourceModel\Content\CollectionFactory
     */
    protected $contentCollectionFactory;

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ContentType $contentTypeModel
     */
    function __construct(
        DirectoryList $directoryList,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        ContentType $contentTypeModel
    ) {
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->csvFactory = $csvFactory;
        $this->directoryList = $directoryList;
        $this->contentTypeModel = $contentTypeModel;
    }

    /**
     * Write collection data in a CSV File
     *
     * @param $importIdentifier
     * @param $select
     * @param $filter
     * @param $fileName
     * @param array $stores
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function exportCsv($importIdentifier, $select, $filter, $fileName, array $stores)
    {
        $path = $this->directoryList->getPath(DirectoryList::TMP) . '/' . $fileName;

        $csvManager = $this->csvFactory->create([
            'destination' => $path,
            'destinationDirectoryCode' => DirectoryList::TMP,
        ]);

        $csvManager->setHeaderCols($this->_getHeaderCols($importIdentifier));

        foreach ($stores as $store) {
            /** @var $collection \Blackbird\ContentManager\Model\ResourceModel\Collection\AbstractCollection */
            $collection = $this->contentCollectionFactory->create();
            $collection->addAttributeToSelect($select);

            $this->_addConditionFilter($collection, $filter);

            $collection->addStoreFilter($store->getId());

            foreach ($collection->toArray() as $entity) {
                $this->_setContentTypeIdentifier($entity);
                $entity['store_code'] = $store->getCode();
                $entityImportIdentifier = (isset($entity['import_identifier'])) ? $entity['import_identifier']
                    : $entity['entity_id'];
                $this->_unsetUselessData($entity);
                $entity = [$importIdentifier => $entityImportIdentifier] + $entity;
                $csvManager->writeRow($entity);
            }
        }
    }

    /**
     * Retrieve the header cols identifiers
     *
     * @param string $importIdentifier
     * @return array
     */
    protected function _getHeaderCols($importIdentifier)
    {
        $customFields = $this->contentTypeModel->getCustomFieldCollection();
        $headerCols = array_merge(['store_code', $importIdentifier], $this->contentTypeModel->getMainAttributes(),
            $customFields->getColumnValues(CustomField::IDENTIFIER));

        // Image type specific case
        foreach ($customFields->getItemsByColumnValue('type', 'image') as $customField) {
            $headerCols[] = $customField->getIdentifier() . '_orig';
            $headerCols[] = $customField->getIdentifier() . '_alt';
            $headerCols[] = $customField->getIdentifier() . '_url';
            $headerCols[] = $customField->getIdentifier() . '_titl';
        }

        return $headerCols;
    }

    /**
     * Add given conditions to the collection
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\Collection\AbstractCollection $collection
     * @param array $filter
     */
    protected function _addConditionFilter($collection, $filter)
    {
        foreach ($filter as $field => $condition) {
            $collection->addAttributeToFilter($field, $condition);
        }
    }

    /**
     * In case if it's a content it will set content type identifier at place of content type id
     *
     * @param array $entity
     */
    protected function _setContentTypeIdentifier(&$entity)
    {
        if (isset($entity['ct_id'])) {
            $contentType = $this->contentTypeModel->load($entity['ct_id']);
            unset($entity['ct_id']);
            $entity['ct_identifier'] = $contentType->getIdentifier();
        }
    }

    /**
     * Unset useless data
     *
     * @param array $entity
     */
    protected function _unsetUselessData(&$entity)
    {
        unset($entity['entity_id'], $entity['created_at'], $entity['updated_at'], $entity['import_identifier']);
    }
}
