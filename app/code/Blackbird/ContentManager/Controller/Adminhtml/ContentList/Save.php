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
 * Class Save
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentList
 */
class Save extends \Blackbird\ContentManager\Controller\Adminhtml\ContentList
{
    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $_validatorFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * Save constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->_validatorFactory = $validatorFactory;
        $this->serializer = $serializer;
        parent::__construct($context, $coreRegistry, $datetime, $contentListCollectionFactory, $modelFactory);
    }

    /**
     * Save action
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->_initAction();
        $contentList = $this->_contentListModel;
        $data = $this->getRequest()->getPostValue();

        if (is_array($data)) {
            // If it does not exists, create a new content list
            if ($contentList instanceof ContentList && is_numeric($contentList->getId())) {
                $data[ContentList::ID] = $contentList->getId();
            } else {
                $contentList = $this->_modelFactory->create(ContentList::class);
            }

            /** Prepare Content List */
            $contentList = $this->prepareContentList($contentList, $data);

            $this->_eventManager->dispatch('contentmanager_contentlype_prepare_save',
                ['post' => $contentList, 'request' => $this->getRequest()]);

            // Save content list
            try {
                $contentList->save();
                $this->messageManager->addSuccessMessage(__('The content list has been saved !'));

                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirect->setPath('*/*/edit',
                        ['id' => $contentList->getClId(), '_current' => true]);
                }

                return $this->resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the content list: %1', $e->getMessage()));
            }

            $this->_getSession()->setFormData($data);

            return $this->resultRedirect->setPath('*/*/edit', ['id' => $contentList->getClId()]);
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare the Content List
     *
     * @param ContentList $contentList
     * @param array $data
     * @return ContentList
     * @throws \Exception
     */
    protected function prepareContentList(ContentList $contentList, array $data)
    {
        // Breadcrumbs (serialize)
        $data = $this->prepareBreadcrumb($data);

        // Conditions (serialize)
        $data = $this->prepareConditions($data, $contentList);

        // Set data to the contentlist
        $contentList->setData($data);

        // Layouts
        if (!empty($data['layout'])) {
            $contentList = $this->prepareLayouts($contentList, $data['layout']);
        }

        return $contentList;
    }

    /**
     * Prepare Breadcrumb (serialize array for saving)
     *
     * @param array $data
     * @return array
     */
    protected function prepareBreadcrumb(array $data)
    {
        if (!empty($data['breadcrumb_prev_name'])) {
            $data['breadcrumb_prev_name'] = serialize($data['breadcrumb_prev_name']);
        } else {
            $data['breadcrumb_prev_name'] = serialize('');
        }

        if (!empty($data['breadcrumb_prev_link'])) {
            $data['breadcrumb_prev_link'] = serialize($data['breadcrumb_prev_link']);
        } else {
            $data['breadcrumb_prev_link'] = serialize('');
        }

        return $data;
    }

    /**
     * Prepare Conditions (serialize array for saving)
     *
     * @param array $data
     * @param $contentList
     * @return array
     */
    protected function prepareConditions(array $data, $contentList)
    {
        $data['conditions'] = $this->serializer->serialize('');
        $contentList->rule->loadPost($data['parameters']);
        if (isset($data['parameters'], $data['parameters']['conditions'])) {
            $data['conditions'] = $this->serializer->serialize($contentList->rule->getConditions()->asArray());
        }

        return $data;
    }

    /**
     * Prepare and save all layouts items of the current content list
     *
     * @param ContentList $contentList
     * @param array $data
     * @return ContentList
     * @throws \Exception
     */
    protected function prepareLayouts(ContentList $contentList, array $data)
    {
        // Set layout configuration data
        $validatorCustomLayout = $this->_validatorFactory->create();

        // Layout update xml
        if ($validatorCustomLayout->isValid($data['xml'])) {
            $contentList->setData(ContentList::LAYOUT_UPDATE_XML, $data['xml']);
        } else {
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }

        // Root template and layout
        $contentList->setData(ContentList::ROOT_TEMPLATE, $data['general']);
        $contentList->setData(ContentList::LAYOUT, $data['template']);

        // Set items to save after the save
        if (isset($data['item']) && is_array($data['item'])) {
            $contentList->setData('after_save_item', $data['item']);
        }

        return $contentList;
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

    /**
     * Retrieves the format for an item and serialize it
     *
     * @param array $item
     * @return string
     */
    protected function getFormatSerialize(array $item)
    {
        $result = serialize([
            'type' => isset($item['format']) ? $item['format'] : '',
            'extra' => isset($item['format_extra']) ? $item['format_extra'] : '',
            'height' => isset($item['format_height']) ? $item['format_height'] : '',
            'width' => isset($item['format_width']) ? $item['format_width'] : '',
            'link' => isset($item['link']) ? $item['link'] : '',
        ]);

        return $result;
    }
}
