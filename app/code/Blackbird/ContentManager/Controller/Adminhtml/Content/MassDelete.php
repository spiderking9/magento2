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

/**
 * Class MassDelete
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class MassDelete extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('id');
        $contentCollection = $this->_contentCollectionFactory->create();

        if (is_array($ids)) {
            $contentCollection->addAttributeToFilter(Content::ID, ['in' => $ids]);
            $records = $contentCollection->count();

            try {
                $contentCollection->delete();
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $records)
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e, __('Something went wrong while deleting the content: %1', $e->getMessage())
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select item(s).'));
        }

        return $this->resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::content_delete');
    }
}
