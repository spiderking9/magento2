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

/**
 * Class Delete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class Delete extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
{
    /**
     * Delete action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initAction();

        try {
            $this->_contentListModel->delete();
            $this->messageManager->addSuccessMessage(__('The content list has been deleted !'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e,
                __('Something went wrong while deleting the content list: %1', $e->getMessage()));
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
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contentlist_delete');
    }
}
