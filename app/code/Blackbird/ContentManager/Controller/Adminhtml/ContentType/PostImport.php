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

namespace Blackbird\ContentManager\Controller\Adminhtml\ContentType;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\FileSystem;

class PostImport extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType\Save
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
     * PostImport constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\FileSystem $fileSystem
     * @param \Magento\Framework\File\UploaderFactory $uploaderFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     * @param \Magento\Config\Model\Config\Source\Locale\Currency $currency
     * @param \Magento\Config\Model\Config\Source\Locale $locale
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        FileSystem $fileSystem,
        UploaderFactory $uploaderFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Magento\Config\Model\Config\Source\Locale\Currency $currency,
        \Magento\Config\Model\Config\Source\Locale $locale
    ) {
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory, $modelFactory,
            $cacheManager, $customFieldsSource, $validatorFactory, $currency, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $file = $this->getRequest()->getFiles();
        $fieldIdentifierExist = false;
        $contentTypeIdentifierExist = false;
        $existingIdentifier = [];

        foreach ($file as $identifier => $dataFile) {
            if (isset($file[$identifier]['name']) && $file[$identifier]['name'] != '') {

                if (file_exists($file[$identifier]['tmp_name'])) {
                    $path = $this->fileSystem->getDirectoryRead(DirectoryList::TMP)->getAbsolutePath();

                    try {
                        $uploader = $this->getUploader($identifier);

                        $dataFile = $uploader->save($path, $file[$identifier]['name']);
                        $dataFileName = $dataFile['path'] . $dataFile['file'];

                        $data = json_decode(file_get_contents($dataFileName), true);
                        $this->getRequest()->setParams($data);

                        if (isset($data['identifier'])) {
                            $contentTypeIdentifierExist = $this->contentTypeIdentifierExists($data['identifier']);
                        }

                        if (isset($data['contenttype']['fieldsets'])) {
                            foreach ($data['contenttype']['fieldsets'] as $id => $fieldset) {
                                if (isset($data['contenttype']['fieldsets'][$id]['fields'])) {
                                    foreach ($data['contenttype']['fieldsets'][$id]['fields'] as $fieldsId => $fields) {
                                        $checkIdentifier = $this->isIdentifierUsed($fields['identifier']);
                                        if ($checkIdentifier) {
                                            $fieldIdentifierExist = true;
                                            array_push($existingIdentifier,
                                                $fields['identifier'] . ' in fieldset ' . $fieldset['title']);
                                        }
                                    }
                                }
                            }
                        }

                        if (!$fieldIdentifierExist && !$contentTypeIdentifierExist) {
                            $this->messageManager->addSuccessMessage(__('Content Type Imported'));
                            $this->_forward('save');
                        } else {
                            if ($contentTypeIdentifierExist) {
                                $this->messageManager->addErrorMessage(__('Content Type identifier already used.'));
                            }

                            if ($fieldIdentifierExist) {
                                $this->messageManager->addErrorMessage(__('Field identifier(s) already used: %1',
                                    implode(', ', $existingIdentifier)));
                            }

                            return $this->resultRedirect->setPath('*/*/import');
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());

                        return $this->resultRedirect->setPath('*/*/import');
                    }
                } else {
                    $this->messageManager->addErrorMessage(__("Import file problem. Format invalid or path not writable (media/contenttype/import)"));

                    return $this->resultRedirect->setPath('*/*/');
                }
            }
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Retrieve the uploader
     *
     * @param string $identifier
     * @return \Magento\Framework\File\Uploader
     */
    private function getUploader($identifier)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $identifier]);
        $uploader->setAllowedExtensions(['json']);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);

        return $uploader;
    }
}
