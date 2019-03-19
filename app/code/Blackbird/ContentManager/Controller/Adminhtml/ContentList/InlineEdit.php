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

use Blackbird\ContentManager\Model\ContentList;

/**
 * Class InlineEdit
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class InlineEdit extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
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
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context, $coreRegistry, $datetime, $contentListCollectionFactory, $modelFactory);
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
        /** @var ContentList $contentList */
        $contentList = $this->_modelFactory->create(ContentList::class);
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
            $contentList->load($ctId);
            try {
                $this->validateData($contentList, $ctData);
                $this->setContentListData($contentList, $ctData);
                $contentList->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithContentListId($contentList, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithContentListId($contentList, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithContentListId($contentList,
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
     * Validate the content list data
     *
     * @param ContentList $contentList
     * @param array $data
     * @throws \Exception
     */
    protected function validateData(ContentList $contentList, array $data)
    {
        if (!$contentList->getId()) {
            throw new \Exception(__('This content list does not exists anymore.'));
        }
        if (!isset($data['url_key']) || $this->contentListUrlKeyExists($data['url_key'], $contentList->getId())) {
            throw new \Exception(__('This URL Key is already uses.'));
        }
    }

    /**
     * Check if content list url_key is unique
     *
     * @param string $urlKey
     * @param int $contentListId
     * @return bool
     */
    protected function contentListUrlKeyExists($urlKey, $contentListId = null)
    {
        $exists = $this->_contentListCollectionFactory->create()->addFieldToFilter(ContentList::URL_KEY, $urlKey);

        if (is_numeric($contentListId)) {
            $exists->addFieldToFilter(ContentList::ID, ['neq' => $contentListId]);
        }

        return ($exists->getSize() > 0);
    }

    /**
     * Set ContentList data
     *
     * @param ContentList $contentList
     * @param array $contentListData
     * @return $this
     */
    public function setContentListData(ContentList $contentList, array $contentListData)
    {
        $contentList->setData(array_merge($contentList->getData(), $contentListData));

        return $this;
    }

    /**
     * Add content list title to error message
     *
     * @param ContentList $contentList
     * @param $errorText
     * @return string
     */
    protected function getErrorWithContentListId(ContentList $contentList, $errorText)
    {
        return '[ContentList ID: ' . $contentList->getId() . '] ' . $errorText;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contentlist_save');
    }
}
