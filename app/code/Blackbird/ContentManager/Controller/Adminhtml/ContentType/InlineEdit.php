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

use Blackbird\ContentManager\Model\ContentType;

/**
 * Class InlineEdit
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentType
 */
class InlineEdit extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * InlineEdit constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory, $modelFactory,
            $cacheManager);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Save action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var ContentType $contentType */
        $contentType = $this->_modelFactory->create(ContentType::class);
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($postItems as $ctId => $ctData) {
            $contentType->load($ctId);
            try {
                $this->validateData($contentType, $ctData);
                $this->setContentTypeData($contentType, $ctData);
                $contentType->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithContentTypeId($contentType, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithContentTypeId($contentType, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithContentTypeId($contentType,
                    __('Something went wrong while saving the content type.'));
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }

    /**
     * Validate the content type data
     *
     * @param ContentType $contentType
     * @param array $data
     * @throws \Exception
     */
    protected function validateData(ContentType $contentType, array $data)
    {
        if (!$contentType->getId()) {
            throw new \Exception(__('This content type does not exists anymore.'));
        }
        if (!isset($data['identifier']) || $this->contentTypeIdentifierExists($data['identifier'],
                $contentType->getId())) {
            throw new \Exception(__('This identifier is already uses.'));
        }
    }

    /**
     * Check if content type identifier is unique
     *
     * @param string $identifier
     * @param int $contentTypeId
     * @return bool
     */
    protected function contentTypeIdentifierExists($identifier, $contentTypeId = null)
    {
        $exists = $this->_contentTypeCollectionFactory->create()
            ->addFieldToFilter(ContentType::IDENTIFIER, $identifier);

        if (is_numeric($contentTypeId)) {
            $exists->addFieldToFilter(ContentType::ID, ['neq' => $contentTypeId]);
        }

        return ($exists->getSize() > 0);
    }

    /**
     * Set ContentType data
     *
     * @param ContentType $contentType
     * @param array $contentTypeData
     * @return $this
     */
    public function setContentTypeData(ContentType $contentType, array $contentTypeData)
    {
        $contentType->setData(array_merge($contentType->getData(), $contentTypeData));

        return $this;
    }

    /**
     * Add content type title to error message
     *
     * @param ContentType $contentType
     * @param $errorText
     * @return string
     */
    protected function getErrorWithContentTypeId(ContentType $contentType, $errorText)
    {
        return '[ContentType ID: ' . $contentType->getId() . '] ' . $errorText;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contenttype_save');
    }
}
