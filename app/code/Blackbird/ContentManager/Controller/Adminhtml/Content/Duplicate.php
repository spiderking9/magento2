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
 * Class Duplicate
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\Content
 */
class Duplicate extends \Blackbird\ContentManager\Controller\Adminhtml\Content
{
    /**
     * @var \Blackbird\ContentManager\Model\Content\Copier
     */
    protected $_contentCopier;

    /**
     * Duplicate constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Blackbird\ContentManager\Model\Content\Copier $contentCopier
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Blackbird\ContentManager\Model\Content\Copier $contentCopier
    ) {
        $this->_contentCopier = $contentCopier;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory,
            $contentCollectionFactory, $modelFactory);
    }

    /**
     * Create content duplicate
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_initAction();

        try {
            $duplicateContent = $this->_contentCopier->copy($this->_contentModel);
            $this->messageManager->addSuccessMessage(__('You duplicated the content.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        if (!empty($duplicateContent) && !$duplicateContent->isObjectNew()) {
            return $this->resultRedirect->setPath('contentmanager/*/edit',
                ['_current' => true, 'id' => $duplicateContent->getId()]);
        }

        return $this->resultRedirect->setPath('contentmanager/*/edit', ['_current' => true]);
    }
}
