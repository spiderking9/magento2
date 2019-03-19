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

use Blackbird\ContentManager\Api\Data\ContentInterface;
use Blackbird\ContentManager\Controller\Adminhtml\Content as ContentController;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ExportHandler;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

class MassExport extends ContentController
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ContentType
     */
    protected $contentTypeModel;

    /**
     * @var ExportHandler
     */
    protected $exportHandler;

    /**
     * Mass Export constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Blackbird\ContentManager\Model\ContentType $contentTypeModel
     * @param \Blackbird\ContentManager\Model\ExportHandler $exportHandler
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        StoreManagerInterface $storeManager,
        ContentType $contentTypeModel,
        ExportHandler $exportHandler
    ) {
        $this->contentTypeModel = $contentTypeModel;
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->exportHandler = $exportHandler;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory,
            $contentCollectionFactory, $modelFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        //Get all selected ids
        $ids = (isset($params['export_all'])) ? $this->_getAllContentIds($params['ct_id']) : $params['id'];

        if (is_array($ids)) {
            //Retrieve the current content type
            $contentTypeId = (isset($params['ct_id'])) ? $params['ct_id'] : $this->_getCurrentCtId($ids);
            $contentType = $this->contentTypeModel->load($contentTypeId);

            $fileName = $contentType->getTitle() . '.csv';

            $this->exportHandler->exportCsv($contentType->getDefaultImportIdentifierName(), '*',
                [ContentInterface::ID => ['in' => $ids]], $fileName, $this->storeManager->getStores(true));

            //Make the exported CSV file downloadable
            try {
                return $this->fileFactory->create('acm-export-content-' . $fileName,
                    ['type' => 'filename', 'value' => $fileName], DirectoryList::TMP, 'application/octet-stream');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $this->resultRedirect->setPath('*/*/', ['ct_id' => $contentTypeId]);
        }
    }

    /**
     * Get all content ids for a content type
     *
     * @param $ctId
     * @return array
     */
    protected function _getAllContentIds($ctId)
    {
        $contentCollection = $this->_contentCollectionFactory->create();

        $contentCollection->addAttributeToSelect('entity_id')->addAttributeToFilter('ct_id', $ctId);

        return array_keys($contentCollection->toArray());
    }

    /**
     * Get the current content type for content ids
     *
     * @param $ids
     * @return mixed
     */
    protected function _getCurrentCtId($ids)
    {
        $contentCollection = $this->_contentCollectionFactory->create();

        $contentCollection->addAttributeToSelect('ct_id')
            ->addAttributeToFilter(ContentInterface::ID, ['in' => $ids])
            ->getSelect()
            ->limit(1);

        return $contentCollection->getFirstItem()->getData('ct_id');
    }
}
