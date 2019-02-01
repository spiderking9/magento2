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

namespace Blackbird\ContentManager\Plugin\Store\Model;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentList;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * Class View
 *
 * @package Blackbird\ContentManager\Plugin\Store\Model
 */
class View
{
    /**
     * @var \Blackbird\ContentManager\Helper\UrlRewriteGenerator
     */
    protected $_urlRewriteHelper;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * View constructor
     *
     * @param \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     */
    public function __construct(
        \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
    ) {
        $this->_urlRewriteHelper = $urlRewriteHelper;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
    }

    /**
     * Delete plugin
     *
     * @param \Magento\Store\Model\ResourceModel\Store $object
     * @param callable $proceed
     * @param AbstractModel $store
     * @return mixed
     */
    public function aroundDelete(
        \Magento\Store\Model\ResourceModel\Store $object,
        callable $proceed,
        AbstractModel $store
    ) {
        $result = $proceed($store);
        $this->_urlRewriteHelper->deleteUrlRewrite(Content::ENTITY, null, $store->getId());

        return $result;
    }

    /**
     * Save plugin
     *
     * @param \Magento\Store\Model\ResourceModel\Store $object
     * @param callable $proceed
     * @param AbstractModel $store
     * @return mixed
     */
    public function aroundSave(
        \Magento\Store\Model\ResourceModel\Store $object,
        callable $proceed,
        AbstractModel $store
    ) {
        $originStore = $store;
        $result = $proceed($originStore);
        if ($store->isObjectNew() || $store->dataHasChangedFor('group_id')) {
            if (!$store->isObjectNew()) {
                $this->_urlRewriteHelper->deleteUrlRewrite([Content::ENTITY, ContentList::ENTITY_TYPE], null,
                    $store->getId());
            }

            $this->generateContentManagerUrlsRewrite($store->getId());
        }

        return $result;
    }

    /**
     * Generate all the url rewrites of the ContentManager module
     *
     * @param int $storeId
     * @return void
     * @throws \Exception
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    protected function generateContentManagerUrlsRewrite($storeId)
    {
        $urls = array_merge($this->generateContentListUrlsRewrite($storeId),
            $this->generateContentUrlsRewrite($storeId));

        $this->_urlRewriteHelper->addUrlRewrites($urls);
    }

    /**
     * Generate url rewrites for content list assigned to store view
     *
     * @param int $storeId
     * @return array
     */
    protected function generateContentListUrlsRewrite($storeId)
    {
        $urls = [];
        $contentListCollection = $this->_contentListCollectionFactory->create()
            ->addStoreFilter([Store::DEFAULT_STORE_ID, $storeId])
            ->addFieldToFilter(ContentList::STATUS, 1)
            ->addFieldToSelect(ContentList::URL_KEY);

        foreach ($contentListCollection as $contentList) {
            $urls[] = [
                'entity_type' => ContentList::ENTITY_TYPE,
                'entity_id' => $contentList->getId(),
                'request_path' => $contentList->getUrlKey(),
                'target_path' => 'contentmanager/index/contentlist/contentlist_id/' . $contentList->getId(),
                'store_id' => $storeId,
            ];
        }

        return $urls;
    }

    /**
     * Generate url rewrites for content assigned to store view
     *
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function generateContentUrlsRewrite($storeId)
    {
        $urls = [];
        $contentCollection = $this->_contentCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addIsVisibleFilter()
            ->addAttributeToSelect(Content::URL_KEY);

        foreach ($contentCollection as $content) {
            $urls[] = [
                'entity_type' => Content::ENTITY,
                'entity_id' => $content->getId(),
                'request_path' => $content->getUrlKey(),
                'target_path' => 'contentmanager/index/content/content_id/' . $content->getId(),
                'store_id' => $storeId,
            ];
        }

        return $urls;
    }
}
