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

use Blackbird\ContentManager\Helper\Content\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\FileSystem;
use Magento\Store\Model\StoreManagerInterface;

class ImportHandler
{
    /**
     * @var Csv
     */
    protected $fileCsv;

    /**
     * @var \Blackbird\ContentManager\Model\ContentFactory
     */
    protected $contentFactory;

    /**
     * @var ContentType
     */
    protected $contentTypeModel;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int
     */
    protected $ctId;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\CsvFactory
     */
    protected $csvFactory;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResourceModel\Content\CollectionFactory
     */
    protected $contentCollectionFactory;

    /**
     * @var String
     */
    protected $importIdentifierCol;

    /**
     * @var bool
     */
    protected $stopAtError;

    /**
     * @var array
     */
    protected $ctIds;

    /**
     * @var \Blackbird\ContentManager\Helper\Content\Data
     */
    protected $helper;

    function __construct(
        \Blackbird\ContentManager\Model\ContentFactory $contentFactory,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        FileSystem $fileSystem,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        ContentType $contentTypeModel,
        Csv $fileCsv,
        Data $helper,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        ManagerInterface $messageManager,
        UploaderFactory $uploaderFactory
    ) {
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->messageManager = $messageManager;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->csvFactory = $csvFactory;
        $this->storeManager = $storeManager;
        $this->contentTypeModel = $contentTypeModel;
        $this->contentFactory = $contentFactory;
        $this->fileCsv = $fileCsv;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->helper = $helper;
    }

    /**
     * Import CSV File to create Contents
     *
     * @param $dataFileName
     * @param $importIdentifierCol
     * @param $stopAtError
     * @param $ctId
     * @throws \Exception
     * @return array
     */
    public function importContentsCsv($dataFileName, $importIdentifierCol, $stopAtError, $ctId)
    {
        $csvData = $this->fileCsv->getData($dataFileName);
        $this->importIdentifierCol = $importIdentifierCol;
        $this->ctId = $ctId;
        $this->stopAtError = $stopAtError;

        //Separate col header (field name) and value parts
        $fields = $csvData[0];
        unset($csvData[0]);

        if (!in_array($this->importIdentifierCol, $fields)) {
            throw new \Exception(__('Import Identifier Column Name has not matched with your CSV file.'));
        }

        return $this->_reformatArray($this->_createContents($this->_sortByIdentifier($fields, $csvData)));
    }

    /**
     * Return a formated array of content data to can be export in CSV
     *
     * @param $contentData
     * @return array
     */
    protected function _reformatArray($contentData)
    {
        $reformattedArray = [];

        foreach ($contentData as $identifier => $content) {
            foreach ($content as $rowId => $data) {
                $data = [$this->importIdentifierCol => $data['import_identifier']] + $data;
                unset($data['import_identifier']);
                $reformattedArray[] = $data;
            }
        }

        return $reformattedArray;
    }

    /**
     * Create Content with imported data if all is good else stock not imported content and set her an error message
     *
     * @param array $contentData
     * @throws \Exception
     * @return array|bool the modified array after import
     */
    protected function _createContents($contentData)
    {
        $lineNumber = 1;

        $identifiers = array_keys($contentData);

        $collection = $this->contentCollectionFactory->create()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('import_identifier', $identifiers)
            ->addContentTypeFilter($this->ctId)
            ->load();

        $contentType = $this->contentTypeModel->load($this->ctId);
        $urlKeyDefault = $contentType->getData('default_url');

        foreach ($contentData as $identifier => $content) {
            $item = $collection->getItemByColumnValue('import_identifier', $identifier);
            $contentId = (!is_null($item)) ? $item->getEntityId() : '';
            foreach ($content as $rowId => $data) {
                try {
                    $newContent = $this->contentFactory->create();
                    /** @var $store \Magento\Store\Model\Store */
                    $store = $this->storeManager->getStore($data['store_code']);

                    if (!empty($contentId) && !empty($identifier)) {
                        $data['entity_id'] = $contentId;
                    }

                    $data['import_identifier'] = $identifier;
                    $data['ct_id'] = $this->ctId;

                    //If the urk_key is somehow not specified we built it with the default one
                    if ($data['url_key'] == '') {
                        $data['url_key'] = $urlKeyDefault;
                    }

                    $this->_unsetUselessData($data);

                    //Save or update the content
                    $newContent->addData($data);

                    foreach ($newContent->getData() as $attribute => $value) {
                        // Apply pattern
                        $value = $this->helper->applyPattern($newContent, $value);
                        $newContent->setData($attribute, $value);
                    }

                    $newContent->setStore($store);
                    $newContent->save();

                    $contentId = $newContent->getId();
                    $lineNumber++;

                    //Need to delete the row id for reformat the array after import
                    unset($contentData[$identifier][$rowId]);
                } catch (\Exception $e) {
                    if ($this->stopAtError) {
                        throw new \Exception(
                            __('Error to import the content at line %1 in your CSV file. Error message : %2',
                            $lineNumber, $e->getMessage())
                        );
                    }
                    $contentData[$identifier][$rowId]['import_identifier'] = $identifier;
                    $contentData[$identifier][$rowId]['error'] = $e->getMessage();
                }
            }
        }

        return $contentData;
    }

    /**
     * Unset useless data
     *
     * @param array $data
     */
    protected function _unsetUselessData(&$data)
    {
        unset($data['store_code'], $data['error'], $data['ct_identifier']);
    }

    /**
     * Format CSV content data to know which contents are news and which contents are the sames
     * in different stores
     *
     * @param $field
     * @param $data
     * @return array
     */
    protected function _sortByIdentifier($field, $data)
    {
        $allContentData = [];
        //Make relation between col headers (attribute name) and values
        foreach ($data as $d) {
            $allContentData[] = array_combine($field, $d);
        }

        $result = [];
        //Format data to have each new and existing content
        foreach ($allContentData as $key => $value) {
            $result[$value[$this->importIdentifierCol]][$key] = $value;
            unset($result[$value[$this->importIdentifierCol]][$key][$this->importIdentifierCol]);
        }

        return $result;
    }

    /**
     * Download a new CSV file with none imported content because error
     *
     * @param $contents
     */
    public function exportErrorContentsCsv($contents)
    {
        $path = $this->directoryList->getPath(DirectoryList::TMP) . '/acm-import-content-error.csv';

        $csvManager = $this->csvFactory->create([
            'destination' => $path,
            'destinationDirectoryCode' => DirectoryList::TMP,
        ]);

        foreach ($contents as $content) {
            $csvManager->writeRow($content);
        }

        try {
            $this->fileFactory->create('acm-import-content-error.csv',
                ['type' => 'filename', 'value' => 'acm-import-content-error.csv'], DirectoryList::TMP,
                'application/octet-stream');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * Get Content Type id by the given identifier
     *
     * @param string $identifier
     * @return int
     */
    protected function _getCtIdByIdentifier($identifier)
    {
        if (isset($this->ctIds[$identifier])) {
            return $this->ctIds[$identifier];
        }
        $contentType = $this->contentTypeModel->load($identifier, 'identifier');
        $contentTypeId = $contentType->getCtId();
        $this->ctIds[$identifier] = $contentTypeId;

        return $contentTypeId;
    }
}
