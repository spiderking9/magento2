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

namespace Blackbird\ContentManager\Controller\Adminhtml\ContentList;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class MassDelete extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
{
    /**
     * Mass actions filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * ContentList constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        $this->filter = $filter;
        parent::__construct($context, $coreRegistry, $datetime, $contentListCollectionFactory, $modelFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initAction();

        try {
            $contentListCollection = $this->filter->getCollection($this->_contentListCollectionFactory->create());
            $records = 0;

            foreach ($contentListCollection as $contentList) {
                try {
                    $contentList->delete();
                    $records++;
                } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage($exception,
                        __('Something went wrong while deleting the content list: %1', $exception->getMessage()));
                }
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $records));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contentlist_delete');
    }
}
