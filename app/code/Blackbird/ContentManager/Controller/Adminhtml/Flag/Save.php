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

namespace Blackbird\ContentManager\Controller\Adminhtml\Flag;

use Blackbird\ContentManager\Model\Flag;

/**
 * Class Save
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Flag
 */
class Save extends \Blackbird\ContentManager\Controller\Adminhtml\Flag
{
    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * Save constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Blackbird\ContentManager\Model\Factory $modelFactory
    ) {
        $this->_modelFactory = $modelFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initAction();

        $data = $this->getRequest()->getPostValue();

        if (is_array($data)) {
            try {
                $this->_saveFlags($data);
                $this->messageManager->addSuccessMessage(__('The flags has been saved !'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the flags: %1', $e->getMessage()));
            }
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Save all flags in DB
     *
     * @param array $data
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _saveFlags(array $data)
    {
        foreach ($data as $key => $value) {
            if (stripos($key, 'store_') !== false) {
                $storeId = str_replace('store_', '', $key);

                // Load or create the flag
                $flagModel = $this->_modelFactory->create(Flag::class)->load($storeId);

                if (is_null($flagModel->getId())) {
                    $flagModel->getResource()->addFlag($storeId, $value);
                } else {
                    $flagModel->setValue($value);
                    $flagModel->save();
                }
            }
        }
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::flag');
    }
}
