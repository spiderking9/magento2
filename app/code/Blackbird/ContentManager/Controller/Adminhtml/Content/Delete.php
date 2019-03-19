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

/**
 * Class Delete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class Delete extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    /**
     * Delete action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initAction();

        if ($this->_contentModel) {
            $contentTypeId = $this->_contentModel->getCtId();
            $storeId = $this->getRequest()->getParam('store');

            // Delete all attributes value for a specified store
            if (is_numeric($storeId)) {
                try {
                    $this->_contentModel->setStoreId($storeId)->load($this->_contentModel->getId());
                    $this->_contentModel->deleteCurrentStoreAttributes();
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e,
                        __('Something went wrong while deleting the content attributes of the current store.'));
                }

                return $this->resultRedirect->setPath('*/*/edit', ['id' => $this->_contentModel->getId()]);

                // Delete the entire content
            } else {
                try {
                    $this->_contentModel->delete();
                    $this->messageManager->addSuccessMessage(__('The content has been deleted !'));
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e,
                        __('Something went wrong while deleting the content: %1', $e->getMessage()));
                }

                return $this->resultRedirect->setPath('*/*/', ['ct_id' => $contentTypeId]);
            }
        } else {
            $this->messageManager->addWarningMessage('Please select a content to delete.');
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::content_delete');
    }
}
