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

namespace Blackbird\ContentManager\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

/**
 * Content retriever helper
 */
class Content extends \Magento\Framework\Url\Helper\Data
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    private $contentCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Initialize the Content to be used for the Content Controller actions and layouts
     *
     * @param int $contentId
     * @param \Magento\Framework\App\Action\Action|null $controller
     * @return bool|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initContent($contentId, $controller = null)
    {
        $this->_eventManager->dispatch(
            'contentmanager_controller_content_init_before',
            ['controller_action' => $controller]
        );

        try {
            $collection = $this->contentCollectionFactory->create()
                ->addStoreFilter()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(\Blackbird\ContentManager\Model\Content::ID, $contentId);

            if (!$collection->count()) {
                throw new NoSuchEntityException(new Phrase('Content entity ID "%1" does not exists.', [$contentId]));
            }

            $content = $collection->getFirstItem();
        } catch (NoSuchEntityException $e) {
            return false;
        }

        // Register current data and dispatch final events
        $this->_coreRegistry->register('current_content', $content);

        try {
            $this->_eventManager->dispatch(
                'contentmanager_controller_content_init_after',
                ['content' => $content, 'controller_action' => $controller]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);

            return false;
        }

        return $content;
    }
}
