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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FileSystem;

class Export extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * Export constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param FileSystem $fileSystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        FileSystem $fileSystem
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->fileSystem = $fileSystem;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory, $modelFactory,
            $cacheManager);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        unset($data['key'], $data['form_key'], $data['id'], $data['back']);

        if (isset($data['contenttype']['fieldsets'])) {
            foreach ($data['contenttype']['fieldsets'] as $id => $fieldset) {
                $data['contenttype']['fieldsets'][$id]['id'] = '0';
                $data['contenttype']['fieldsets'][$id]['fieldset_id'] = '0';

                if (isset($data['contenttype']['fieldsets'][$id]['fields'])) {
                    foreach ($data['contenttype']['fieldsets'][$id]['fields'] as $fieldsId => $fields) {
                        $data['contenttype']['fieldsets'][$id]['fields'][$fieldsId]['id'] = '0';
                        $data['contenttype']['fieldsets'][$id]['fields'][$fieldsId]['option_id'] = '0';

                        if (isset($data['contenttype']['fieldsets'][$id]['fields'][$fieldsId]['select'])) {
                            foreach ($data['contenttype']['fieldsets'][$id]['fields'][$fieldsId]['select'] as $selectId
                            => $selectValue) {
                                $data['contenttype']['fieldsets'][$id]['fields'][$fieldsId]['select'][$selectId]['option_type_id'] = '-1';
                            }
                        }
                    }
                }
            }
        }

        unset($data['layout']);

        return $exportFileResponse = $this->fileFactory->create('acm-export-' . $data['identifier'] . '.json',
            ['type' => 'filename', 'value' => $this->generateFile($data)], DirectoryList::TMP,
            'application/octet-stream');
    }

    /**
     * Generate the JSON file with all content type data
     *
     * @param $data
     * @return string
     * @throws LocalizedException
     */
    public function generateFile($data)
    {
        $fileName = 'acm-export-' . $data['identifier'] . '.json';

        $writer = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $file = $writer->openFile($fileName, 'w');
        $file->write(json_encode($data));
        $file->close();

        return $fileName;
    }
}
