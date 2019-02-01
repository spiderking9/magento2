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

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ContentType\CustomField;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use Magento\Store\Model\Store;

/**
 * Class Save
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class Save extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    const ADMIN_RESOURCE = 'Blackbird_ContentManager::content_save';

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $_customFieldsSource;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_driverFile;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $_urlDecoder;

    /**
     * @var \Blackbird\ContentManager\Helper\Content\Data
     */
    protected $_helperContent;

    /**
     * @var bool
     */
    protected $_hasProcessedRepeaterFiles = false;

    /**
     * @var array
     */
    protected $_repeaterFiles = [];

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Blackbird\ContentManager\Helper\Content\Data $helperContent
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Blackbird\ContentManager\Helper\Content\Data $helperContent
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_customFieldsSource = $customFieldsSource;
        $this->_filesystem = $filesystem;
        $this->_driverFile = $driverFile;
        $this->_urlDecoder = $urlDecoder;
        $this->_helperContent = $helperContent;
        parent::__construct(
            $context,
            $coreRegistry,
            $datetime,
            $contentTypeCollectionFactory,
            $contentCollectionFactory,
            $modelFactory
        );
    }

    /**
     * Save action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_initAction();
        $contentType = $this->_contentTypeModel;
        $content = $this->_contentModel;
        $postData = $this->getRequest()->getPostValue();
        $this->_getSession()->setFormData($postData);
        $storeId = !empty($this->getRequest()->getParam('store'))
            ? $this->getRequest()->getParam('store')
            : Store::DEFAULT_STORE_ID;

        // If request is correctly processed and the contentype exists
        if (is_array($postData) && $contentType instanceof ContentType && !empty($contentType->getCtId())) {
            if ($content instanceof Content && $content->getId()) {
                $content->addData([
                    Content::UPDATED_AT => $this->_datetime->date(),
                    Content::CT_ID => $contentType->getCtId(),
                ]);
            } else {
                /** @var Content $content */
                $content = $this->_modelFactory->create(
                    Content::class,
                    ['data' => [
                        Content::CT_ID => $contentType->getCtId(),
                        Content::STORE_ID => $storeId,
                        Content::CREATED_AT => $this->_datetime->date(),
                        Content::TITLE => $postData[Content::TITLE],
                        Content::STATUS => $postData[Content::STATUS],
                    ]]
                );

                // Always create content for store ID '0'
                if ($storeId != Store::DEFAULT_STORE_ID) {
                    $content->addData([
                        Content::STATUS => Enabledisable::DISABLE_VALUE,
                        Content::STORE_ID => Store::DEFAULT_STORE_ID,
                    ]);
                }

                try {
                    $content = $this->applyData(
                        $content,
                        !empty($postData[Content::URL_KEY]) ? [Content::URL_KEY => $postData[Content::URL_KEY]] : []
                    );
                    $content->setData('ignore_generate_urls', true);
                    $content->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirect->setRefererOrBaseUrl();
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirect->setRefererOrBaseUrl();
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e, __('Something went wrong while saving the content: %1', $e->getMessage())
                    );
                    return $this->resultRedirect->setRefererOrBaseUrl();
                }
            }

            // Save content
            try {
                $content = $this->prepareContent($content, $postData);

                $this->_eventManager->dispatch(
                    'contentmanager_content_prepare_save',
                    ['post' => $content, 'request' => $this->getRequest()]
                );

                $content->setStoreId($storeId);
                $content->setData('ignore_generate_urls', false);
                $content->save();

                $this->messageManager->addSuccessMessage(__('The content has been saved !'));
                $this->_getSession()->setFormData(false);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e, __('Something went wrong while saving the content: %1', $e->getMessage())
                );
            }

            /** Redirect Manager */
            switch ($this->getRequest()->getParam('back', 'edit')) {
                case 'new':
                    $path = '*/*/new';
                    $params = ['ct_id' => $content->getCtId()];
                    break;
                case 'duplicate':
                    $path = '*/*/duplicate';
                    $params = ['id' => $content->getId()];
                    break;
                case 'edit':
                    $path = '*/*/edit';
                    $params = ['id' => $content->getId(), '_current' => true];
                    break;
                case 'back':
                default:
                    $path = '*/*/';
                    $params = ['ct_id' => $content->getCtId()];
                    break;
            }

            return $this->resultRedirect->setPath($path, $params);
        }

        $this->messageManager->addErrorMessage(__('This content type no longer exists.'));

        return $this->resultRedirect->setPath('*/contenttype/');
    }

    /**
     * Apply data
     *
     * @param \Blackbird\ContentManager\Model\Content $content
     * @param array $data
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function applyData(Content $content, array $data)
    {
        /** @var CustomField $customField */
        foreach ($content->getContentType()->getCustomFieldCollection() as $customField) {
            if ($customField->getType() == 'image' && isset($data[$customField->getIdentifier()]) && $data[$customField->getIdentifier()] == '' &&
                (!isset($data['content_image']) || !isset($data['content_image'][$customField->getIdentifier()]))) {
                $content->setData($customField->getIdentifier() . '_orig', null);
            }

            $content->setData($customField->getIdentifier(), $data[$customField->getIdentifier()] ?? null);
        }
        foreach ($content->getContentType()->getMainAttributes() as $attribute) {
            $content->setData($attribute, $data[$attribute] ?? null);
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            if (is_string($value)) {
                $value = $this->_helperContent->applyPattern($content, $value);
            }
            $content->setData($key, $value);
        }

        return $content;
    }

    /**
     * Prepare Content to be saved
     *
     * @param \Blackbird\ContentManager\Model\Content $content
     * @param array $data
     * @param bool $fromRepeater
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareContent(Content $content, array $data, $fromRepeater = false)
    {
        //prepare repeaters
        $data = $this->prepareRepeaterFields($content, $data);
        // Handle file management
        $data = $this->prepareFiles($data, $fromRepeater);

        return $this->applyData($content, $data);
    }

    /**
     * @param Content $content
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareRepeaterFields(Content $content, array $data)
    {
        // REPEATER FIELDS
        $contentType = $content->getContentType();

        if ($contentType) {
            $collectionCustomFields = $contentType->getCustomFieldCollection()
                ->addFieldToFilter('type', 'repeater_fields');
            $content->getContentType();

            /** If this content type owns repeater fields */
            if ($collectionCustomFields->count() > 0) {
                foreach ($collectionCustomFields as $customField) {
                    $contentIds = [];

                    $idsToDelete = explode(',', trim($data['repeater-field']['repeaters-to-delete'], ','));

                    if (array_key_exists($customField->getIdentifier(), $data['repeater-field'])) {

                        foreach ($data['repeater-field'][$customField->getIdentifier()] as $key => $repeaterAttributes)
                        {
                            /* retrieve Content Type ID of the repeater Field*/
                            $repeaterFieldCtIdentifier = $customField->getData('content_type');
                            $repeaterContentType = $this->_contentTypeCollectionFactory->create()
                                ->addFieldToFilter(ContentType::IDENTIFIER, $repeaterFieldCtIdentifier)
                                ->getFirstItem();

                            $contentRepeater = null;
                            $toDelete = false;

                            // check if the repeater content needs to be created or just updated
                            if ((isset($repeaterAttributes['content_id'])) && $repeaterAttributes['content_id'] != null) {
                                $contentRepeater = $this->_modelFactory->create(Content::class)
                                    ->setId($repeaterAttributes['content_id']);

                                //Check if this repeater field should be deleted
                                if (in_array($repeaterAttributes['content_id'], $idsToDelete)) {
                                    $toDelete = true;
                                }

                                unset($repeaterAttributes['content_id']);
                            }

                            $dataRepeater = [];
                            if (!$toDelete) {
                                //update or create a new content
                                if (is_array($data) && $contentType instanceof ContentType && !empty($repeaterContentType->getCtId())) {
                                    // If we are editing an existing content
                                    if ($contentRepeater instanceof Content && is_numeric($contentRepeater->getId())) {
                                        // Update the last edit time
                                        $dataRepeater[Content::UPDATED_AT] = $this->_datetime->date();
                                        $dataRepeater[Content::CT_ID] = $repeaterContentType->getCtId();
                                        $dataRepeater[Content::ID] = $contentRepeater->getId();
                                    } else {
                                        // ...else we create a new content
                                        $contentRepeater = $this->_modelFactory->create(Content::class);
                                        $dataRepeater[Content::CREATED_AT] = $this->_datetime->date();
                                        $dataRepeater[Content::CT_ID] = $repeaterContentType->getCtId();
                                    }
                                }


                                /*Set Data*/
                                if (array_key_exists('repeater-field', $data)) {
                                    $dataRepeater = array_merge($dataRepeater, $repeaterAttributes);
                                }

                                $storeId = !empty($this->getRequest()->getParam('store'))
                                    ? $this->getRequest()->getParam('store')
                                    : Store::DEFAULT_STORE_ID;

                                $contentRepeater->setCtId($repeaterContentType->getCtId());
                                $contentRepeater->setStoreId($storeId);
                                $contentRepeater = $this->prepareContent($contentRepeater, $dataRepeater, true);

                                $dataRepeater = $this->deleteRepeaterFiles($dataRepeater, $key);
                                $this->applyData($contentRepeater, $dataRepeater);


                                // Take care of setting the data to save a file
                                $this->saveRepeaterFiles($contentRepeater, $key);

                                try {
                                    //Save of the content repeater
                                    $contentIds[] = $contentRepeater->save()->getId();
                                    $this->_getSession()->setFormData(false);
                                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                    $this->messageManager->addErrorMessage($e->getMessage());
                                } catch (\RuntimeException $e) {
                                    $this->messageManager->addErrorMessage($e->getMessage());
                                } catch (\Exception $e) {
                                    $this->messageManager->addExceptionMessage($e,
                                        __('Something went wrong while saving a repeater field: %1', $e->getMessage()));
                                }
                            } else {

                                try {
                                    //Delete the repeater content
                                    $contentRepeater->delete();
                                } catch (\Exception $e) {
                                    $this->messageManager->addExceptionMessage($e,
                                        __('Something went wrong while deleting a repeater field: %1',
                                            $e->getMessage()));
                                }
                            }
                        }
                    }

                    $data[$customField->getIdentifier()] = $contentIds;
                }
            }

        }
        unset($data['repeater-field']);
        unset($data['delete-repeater-file']);

        return $data;
    }

    /**
     * Delete repeater fields files
     *
     * @param array $dataRepeater
     * @param $indexRepeater
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteRepeaterFiles(array $dataRepeater, $indexRepeater)
    {
        if (is_array($this->getRequest()->getParam('delete-repeater-file'))) {
            foreach ($this->getRequest()->getParam('delete-repeater-file') as $index => $identifier) {
                if ($indexRepeater == $index) {
                    if (array_key_exists($identifier, $dataRepeater)) {
                        $this->deleteFile($identifier, $dataRepeater[$identifier]);
                        $dataRepeater[$identifier] = null;
                    }
                }
            }
        }

        return $dataRepeater;
    }

    /**
     * Delete the given filename
     *
     * @param string $identifier
     * @param string $filename
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteFile($identifier, $filename)
    {
        $fileField = $this->_customFieldsSource->getCustomFieldsByIdentifiers($identifier)->getFirstItem();
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fileField->getFilePath());
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath(ContentType::CT_FILE_FOLDER . DIRECTORY_SEPARATOR . $filePath);
        $path .= DIRECTORY_SEPARATOR . $filename;

        $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getDriver()->deleteFile($path);
    }

    /**
     * Use the class variable $_repeaterFiles to save the file for repeaters fields
     *
     * @param $content
     * @param $index
     */
    protected function saveRepeaterFiles($content, $index)
    {
        $repeaterFiles = $this->getRepeaterFiles();

        if ($repeaterFiles && array_key_exists($index, $repeaterFiles)) {
            foreach ($repeaterFiles[$index] as $key => $file) {
                $content->setData($key, $file);
            }
        }
    }

    /**
     * Retrieve the repeater fields files
     *
     * @return array
     */
    public function getRepeaterFiles()
    {
        return $this->_repeaterFiles;
    }

    /**
     * Set the repeater fields files
     *
     * @param $repeaterFiles
     * @return $this
     */
    public function setRepeaterFiles($repeaterFiles)
    {
        $this->_repeaterFiles = $repeaterFiles;

        return $this;
    }

    /**
     * Prepare files to save
     *
     * @param array $data
     * @param bool $fromRepeater
     * @return array
     */
    protected function prepareFiles(array $data, $fromRepeater)
    {
        // Manage files
        if (!$fromRepeater) {
            $data = $this->manageFiles($data);
        } elseif ($fromRepeater && !$this->_hasProcessedRepeaterFiles) {
            $data = $this->manageRepeaterFiles($data);
        }



        // Manage images
        if (!empty($data['content_image']) && is_array($data['content_image'])) {
            $dataImages = $this->manageImages($data['content_image']);

            unset($data['content_image']);
            $data = array_merge($data, $dataImages);
        }

        return $data;
    }

    /**
     * Save, replace and delete files
     *
     * @param array $data
     * @return array
     */
    protected function manageFiles(array $data)
    {
        $dataFiles = [];
        $files = (array)$this->getRequest()->getFiles();

        // Prevent images files
        unset($files['content_image']);

        // Download files
        try {
            $dataFiles = $this->_uploadFiles($files);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        foreach ($dataFiles as $dataFile) {
            $data = array_merge($data, $dataFile);
        }

        // File replacement
        $data = $this->replaceFiles($dataFiles, $data);

        // File deletion
        $data = $this->deleteFiles($data);

        return $data;
    }

    /**
     * Upload the files and return an array of data
     *
     * @param array $files
     * @param bool $fromRepeater
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _uploadFiles(array $files = [], $fromRepeater = false)
    {
        $results = [];
        $files = (!empty($files)) ? $files : (array)$this->getRequest()->getFiles();

        foreach ($files as $identifier => $dataFile) {
            if (!empty($dataFile['name'])) {
                if ($this->_driverFile->isExists($dataFile['tmp_name'])) {
                    $fileField = $this->_customFieldsSource->getCustomFieldsByIdentifiers($identifier)->getFirstItem();
                    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fileField->getFilePath());

                    $allowedExtensions = explode(',', $fileField->getData(CustomField::FILE_EXTENSION));
                    $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath(ContentType::CT_FILE_FOLDER . DIRECTORY_SEPARATOR . $filePath);

                    try {
                        if ($fromRepeater) {
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => $dataFile]);
                        } else {
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => $identifier]);
                        }

                        $uploader->setAllowedExtensions($allowedExtensions);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        try {
                            $fileData = $uploader->save($path, $dataFile['name']);
                            $dataFileName = $fileData['file'];

                            $results[] = [$identifier => $dataFileName];
                        } catch (\Exception $e) {
                            $this->messageManager->addWarningMessage($e->getMessage());
                            $results[] = [];
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage(
                            $e, __('Something went wrong while upload the file.')
                        );
                    }

                } else {
                    throw new \Exception('The file ' . $identifier . ' has encountered a problem during the upload.');
                }
            }
        }

        return $results;
    }

    /**
     * Replace file if same identifiers are matched
     *
     * @param array $dataFiles
     * @param array $data
     * @return array
     */
    protected function replaceFiles(array $dataFiles, array $data)
    {
        foreach ($dataFiles as $dataFile) {
            foreach ($dataFile as $identifier => $fileName) {
                if (!empty($data[$identifier]) && $data[$identifier] != $fileName) {
                    $data[$identifier] = $dataFile[$identifier];
                }
            }
        }

        return $data;
    }

    /**
     * Delete requested files
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteFiles(array $data)
    {
        $deleteIds = $this->getRequest()->getParam('delete');
        if (is_array($deleteIds)) {
            foreach ($deleteIds as $identifier) {
                $this->deleteFile($identifier, $data[$identifier]);
                $data[$identifier] = null;
            }
        }

        return $data;
    }

    /**
     * Manage the files upload for repeater fields
     *
     * @param array $data
     * @return array
     */
    protected function manageRepeaterFiles(array $data)
    {
        $this->_hasProcessedRepeaterFiles = true;

        $dataFiles = [];
        $files = (array)$this->getRequest()->getFiles();
        $repeaterFiles = [];

        // Check if there is files to process or not
        if (!isset($files['repeater-field'])) {
            return $data;
        }

        /* Manage files for Repeater Field */
        foreach ($files['repeater-field'] as $key => $repeater) {
            foreach ($repeater as $identifier => $repeaterFile) {
                unset($repeaterFile['content_image']);
                $repeaterFiles[$identifier] = $repeaterFile;
            }
        }

        try {
            $dataFilesRepeater = [];
            foreach ($repeaterFiles as $key => $file) {
                $dataFilesRepeater[] = $this->_uploadFiles($file, true);
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        foreach ($dataFilesRepeater as $index => $repeaterFile) {
            if (array_key_exists(0, $repeaterFile)) {
                $dataFiles[$index] = $repeaterFile[0];
            }
        }

        foreach ($dataFiles as $dataFile) {
            $data = array_merge($data, $dataFile);
        }

        //File replacement
        $data = $this->replaceFiles($dataFiles, $data);

        // Fill the class variable with the files.
        $this->setRepeaterFiles($dataFiles);

        return $data;
    }

    /**
     * Save original and cropped images
     *
     * @todo check if name with suffix '_orig' does not already exists
     * @param array $data
     * @return array
     */
    protected function manageImages(array $data)
    {
        $dataImage = [];
        $imageFields = $this->_customFieldsSource->getCustomFieldsByIdentifiers(array_keys($data));

        // Contain images data by field identifier
        foreach ($data as $key => $json) {

            // Init vars
            $image = json_decode($json);
            $imageField = $imageFields->getItemByColumnValue(CustomField::IDENTIFIER, $key);
            $path = '';
            $extensions = [];

            /**
             * If json_decode return null
             *
             * @note html5uploadimage: basic script send json limited to 524288 chars
             * provided by the input text, with attribute name '..._values'. Change
             * that input type to hidden in order to have unlimited chars.
             * Warning: check your config for more details about limitation :
             * Apache: LimitRequestBody
             * PHP: post_max_size
             */
            if (!$image) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong during saving the image of the field "%1"', $key)
                );
                continue;
            }

            // Retrieve the additional file path
            if (!empty($imageField->getFilePath())) {
                $path = DIRECTORY_SEPARATOR . $imageField->getFilePath();
            }

            // Retrieve compatible file extensions
            if (!empty($imageField->getData(CustomField::FILE_EXTENSION))) {
                $extensions = explode(',', $imageField->getData(CustomField::FILE_EXTENSION));
            }

            /**
             * The module send a url or a base 64 for the sended picture html5uploader
             */

            // Original Image
            if (!empty($image->original)) {
                // Only if the format is not an url and so a base64 data
                if (filter_var($image->original, FILTER_VALIDATE_URL) === false) {
                    try {
                        $dataImage[$key . '_orig'] = $this->_saveImage($image->name, $image->original, $extensions,
                            $path);
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage(
                            $e, __('An error has been occurred for the field "%1" : %2', $key, $e->getMessage())
                        );
                        unset($dataImage[$key . '_orig']);

                        continue;
                    }
                } else {
                    $filename = explode('/', $image->original);
                    $dataImage[$key . '_orig'] = end($filename);
                }
            } else {
                $image->data = null;
                $dataImage[$key . '_orig'] = '';
            }
            // Croped Image
            if (!empty($image->data)) {
                // Only if the croop toll is enabled and value is not an url and so a base64 data
                if ($imageField->getCrop() && filter_var($image->data, FILTER_VALIDATE_URL) === false) {
                    $path = DIRECTORY_SEPARATOR . ContentType::CT_IMAGE_CROPPED_FOLDER . $path;
                    try {
                        $dataImage[$key] = $this->_saveImage($image->name, $image->data, $extensions, $path);
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e,
                            __('An error has been occurred for the field "%1" : %2', $key, $e->getMessage()));
                        unset($dataImage[$key]);
                        continue;
                    }
                } elseif (!empty($dataImage[$key . '_orig'])) {
                    // If the crop tool is disabled, use the original image
                    $dataImage[$key] = $dataImage[$key . '_orig'];
                }
            } else {
                $dataImage[$key] = '';
            }
        }

        return $dataImage;
    }

    /**
     * Save a picture from it's base64
     *
     * @param string $filename
     * @param string $data
     * @param array $extensions
     * @param string $subFolder
     * @return string
     * @throws \Exception
     */
    protected function _saveImage($filename, $data, array $extensions = [], $subFolder = '')
    {
        /**
         * $data is formated like this :
         * data:image/png;base64,iVBORw0KGgoAAAANSU....
         */
        $data = explode(';', $data);
        $mime = explode('/', $data[0]);
        $base64 = explode(',', $data[1]);
        $base64 = $base64[1];
        $directoryWrite = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $filePath = ContentType::CT_FILE_FOLDER . $subFolder;

        // Stop save if not a compatible image
        if (!$this->_isImageAllowed($mime, $extensions)) {
            throw new \Exception(
                __('The file "%1" is not of these allowed formats : "%2".', $filename, implode(', ', $extensions))
            );
        }

        // Create the sub folder if it doesn't exists
        try {
            $directoryWrite->create($filePath);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        // Get new file name if the same is already exists
        $destinationFile = $directoryWrite->getAbsolutePath($filePath . DIRECTORY_SEPARATOR . $filename);
        $filename = Uploader::getNewFileName($destinationFile);

        $filePath .= DIRECTORY_SEPARATOR . $filename;

        // Save image
        $directoryWrite->writeFile($filePath, $this->_urlDecoder->decode($base64));

        return $filename;
    }

    /**
     * Check if the format of the image is correct
     *
     * @param array $mime
     * @param array $extensions
     * @return boolean
     */
    protected function _isImageAllowed($mime, array $extensions = [])
    {
        $mime[0] = str_replace('data:', '', $mime[0]);

        return ($mime[0] === 'image' && $this->_isFileExtensionsAllowed($mime[1], $extensions));
    }

    /**
     * Check if extensions are allowed
     *
     * @param string $mime
     * @param array $extensions
     * @return boolean
     */
    protected function _isFileExtensionsAllowed($mime, array $extensions = [])
    {
        $isAllowed = true;
        $extensions = array_filter(array_map('trim', $extensions));

        // Special case of jpg and svg+xml format
        if (in_array('jpg', $extensions) && !in_array('jpeg', $extensions)) {
            $extensions[] = 'jpeg';
        }
        if (!in_array('jpg', $extensions) && in_array('jpeg', $extensions)) {
            $extensions[] = 'jpg';
        }
        if (in_array('svg', $extensions) && !in_array('svg+xml', $extensions)) {
            $extensions[] = 'svg+xml';
        }
        if (!in_array('svg', $extensions) && in_array('svg+xml', $extensions)) {
            $extensions[] = 'svg';
        }

        // If no allowed extensions is given, all extensions are allowed
        if (!empty($extensions)) {
            $isAllowed = in_array($mime, $extensions);
        }

        return $isAllowed;
    }
}
