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

namespace Blackbird\ContentManager\Controller\Adminhtml\Content;

use Blackbird\ContentManager\Controller\Adminhtml\Content;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ImportHandler;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\FileSystem;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class PostImport extends Content
{
    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

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
     * @var ImportHandler
     */
    protected $importHandler;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * Post Import constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Blackbird\ContentManager\Model\ContentFactory $contentFactory
     * @param \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\FileSystem $fileSystem
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Blackbird\ContentManager\Model\ContentType $contentTypeModel
     * @param \Magento\Framework\File\Csv $fileCsv
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Blackbird\ContentManager\Model\ImportHandler $importHandler
     * @param \Magento\Framework\File\UploaderFactory $uploaderFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Blackbird\ContentManager\Model\ContentFactory $contentFactory,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        FileSystem $fileSystem,
        DirectoryList $directoryList,
        ContentType $contentTypeModel,
        Csv $fileCsv,
        StoreManagerInterface $storeManager,
        ImportHandler $importHandler,
        UploaderFactory $uploaderFactory
    ) {
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->importHandler = $importHandler;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->csvFactory = $csvFactory;
        $this->storeManager = $storeManager;
        $this->contentTypeModel = $contentTypeModel;
        $this->contentFactory = $contentFactory;
        $this->fileCsv = $fileCsv;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory,
            $contentCollectionFactory, $modelFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $file = $this->getRequest()->getFiles();
        $params = $this->getRequest()->getParams();
        $this->ctId = $params['ct_id'];
        $importIdentifierCol = $params['import_identifier'];
        $stopAtError = isset($params['stop_at_error']);

        foreach ($file as $identifier => $dataFile) {
            if (isset($file[$identifier]['name']) && $file[$identifier]['name'] != '') {
                if (file_exists($file[$identifier]['tmp_name'])) {
                    $path = $this->fileSystem->getDirectoryRead(DirectoryList::TMP)->getAbsolutePath();
                    try {
                        $uploader = $this->getUploader($identifier);
                        //Retrieve data from uploaded CSV file
                        $dataFile = $uploader->save($path, $file[$identifier]['name']);
                        $dataFileName = $dataFile['path'] . $dataFile['file'];

                        //Create each imported content and return content which catch error
                        $afterImportData = $this->importHandler->importContentsCsv($dataFileName, $importIdentifierCol,
                            $stopAtError, $this->ctId);

                        if (empty($afterImportData)) {
                            $this->messageManager->addSuccessMessage(__('Contents imported!'));
                        } else {
                            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                                ->setDuration(36000)
                                ->setPath('/admin')
                                ->setDomain($this->_session->getCookieDomain());
                            $this->cookieManager->setPublicCookie('downloadError', 1, $metadata);
                            $this->messageManager->addWarningMessage(__('Some content imported but not all.'));
                            $this->importHandler->exportErrorContentsCsv($afterImportData);
                        }

                        return $this->resultRedirect->setPath('*/*/import', ['ct_id' => $this->ctId]);
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());

                        return $this->resultRedirect->setPath('*/*/import', ['ct_id' => $this->ctId]);
                    }
                }
            }
        }

        return $this->resultRedirect->setPath('*/*/import', ['ct_id' => $this->ctId]);
    }

    /**
     * Get the file uploader for CSV file
     *
     * @param $identifier
     * @return \Magento\Framework\File\Uploader
     */
    private function getUploader($identifier)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $identifier]);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);

        return $uploader;
    }
}
