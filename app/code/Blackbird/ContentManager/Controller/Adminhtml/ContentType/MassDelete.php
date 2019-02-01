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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentType
 */
class MassDelete extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
{
    /**
     * Mass actions filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        $this->filter = $filter;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory, $modelFactory,
            $cacheManager);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initAction();

        try {
            $contentTypeCollection = $this->filter->getCollection($this->_contentTypeCollectionFactory->create());
            $records = 0;

            // Delete content types
            foreach ($contentTypeCollection as $contentType) {
                try {
                    $contentType->delete();
                    $records++;
                } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage($exception,
                        __('Something went wrong while deleting the content type: %1', $exception->getMessage()));
                }
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $records));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        // Flush backend main menu cache
        $this->flushBackendMainMenuCache();

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contenttype_delete');
    }
}
