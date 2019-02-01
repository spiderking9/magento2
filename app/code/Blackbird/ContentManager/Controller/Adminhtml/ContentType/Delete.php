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

/**
 * Class Delete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentType
 */
class Delete extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
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
            $this->_contentTypeModel->delete();
            $this->messageManager->addSuccessMessage(__('The content type has been deleted !'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e,
                __('Something went wrong while deleting the content type: %1', $e->getMessage()));
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
