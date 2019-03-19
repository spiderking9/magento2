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

namespace Blackbird\ContentManager\Model;

use Blackbird\ContentManager\Api\Data\ContentType\Layout\ItemInterface as Item;
use Blackbird\ContentManager\Model\ResourceModel\ContentList as ResourceContentList;

class ContentList extends \Blackbird\ContentManager\Model\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentListInterface,
    \Blackbird\ContentManager\Api\ContentLayoutInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Content manager content cache tag
     */
    const CACHE_TAG = 'contentmanager_contentlist';

    const ENTITY_TYPE = 'contenttype_contentlist';
    /**
     * @var \Blackbird\ContentManager\Model\Rule
     */
    public $rule;
    /**
     * Available layout item
     * (even put group in first position)
     *
     * @var array
     */
    protected $_availableLayoutItem = [
        'group',
        'field',
        'block',
    ];
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Group\CollectionFactory
     */
    protected $_groupItemCollectionFactory;
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Field\CollectionFactory
     */
    protected $_fieldItemCollectionFactory;
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Block\CollectionFactory
     */
    protected $_blockItemCollectionFactory;
    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlManager;
    /**
     * @var \Blackbird\ContentManager\Model\Rule\Condition\Sql\Builder
     */
    protected $_sqlBuilder;

    /**
     * @var \Blackbird\ContentManager\Helper\UrlRewriteGenerator
     */
    protected $_urlRewriteHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Factory $modelFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlManager
     * @param ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param ResourceContentList\Layout\Group\CollectionFactory $groupItemCollectionFactory
     * @param ResourceContentList\Layout\Field\CollectionFactory $fieldItemCollectionFactory
     * @param ResourceContentList\Layout\Block\CollectionFactory $blockItemCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param Rule\Condition\Sql\Builder $sqlBuilder
     * @param \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlManager,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Group\CollectionFactory $groupItemCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Field\CollectionFactory $fieldItemCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Block\CollectionFactory $blockItemCollectionFactory,
        \Blackbird\ContentManager\Model\RuleFactory $ruleFactory,
        \Blackbird\ContentManager\Model\Rule\Condition\Sql\Builder $sqlBuilder,
        \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_modelFactory = $modelFactory;
        $this->_storeManager = $storeManager;
        $this->_urlManager = $urlManager;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_groupItemCollectionFactory = $groupItemCollectionFactory;
        $this->_fieldItemCollectionFactory = $fieldItemCollectionFactory;
        $this->_blockItemCollectionFactory = $blockItemCollectionFactory;
        $this->rule = $ruleFactory->create();
        $this->_sqlBuilder = $sqlBuilder;
        $this->_urlRewriteHelper = $urlRewriteHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [
            self::CACHE_TAG . '_' . $this->getId(),
            ContentType::CACHE_TAG . '_' . $this->getContentType()->getId(),
        ];
    }

    /**
     * Get the content type
     *
     * @return ContentType
     */
    public function getContentType()
    {
        if (!$this->hasData('content_type')) {
            $contentType = $this->_modelFactory->create(ContentType::class)->load($this->getCtId());
            $this->setData('content_type', $contentType);
        }

        return $this->getData('content_type');
    }

    /**
     * Retrieve the content collection from the content list
     *
     * @param bool $reload
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     */
    public function getContentCollection($reload = false)
    {
        if (!$this->hasData('content_collection') || $reload) {
            $collection = $this->_contentCollectionFactory->create()
                ->addStoreFilter()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(Content::STATUS, 1)
                ->addContentTypeFilter($this->getContentType())
                ->addAttributeToSort($this->getOrderField(), $this->getSortOrder())
                ->setPageSize($this->getLimitPerPage());
            $collection->getSelect()->limit($this->getLimitDisplay());

            // Set conditions to the collection
            $this->rule->setConditionsSerialized($this->getConditions());
            $conditions = $this->rule->getConditions();
            $conditions->collectValidatedAttributes($collection);
            $this->_sqlBuilder->attachConditionToCollection($collection, $conditions);

            $this->setData('content_collection', $collection);
        }

        return $this->getData('content_collection');
    }

    /**
     * Retrieve the url key (UrlKey as Request Path)
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlKey();
    }

    /**
     * Retrieve the current content list url
     *
     * @todo refactor
     * @param Store|int|string $store
     * @param bool $preview
     * @return string
     */
    public function getContentListUrl($store = null, $preview = false)
    {
        $query = [];
        if ($preview) {
            $query['preview'] = 1;
        }
        if ($store instanceof \Magento\Store\Model\StoreManagerInterface) {
            $store = $store->getCode();
        } elseif (is_numeric($store)) {
            $store = $this->_storeManager->getStore($store)->getCode();
        }
        if (!empty($store) && is_string($store)) {
            $query['___store'] = $store;
        }

        return $this->_urlManager->getDirectUrl($this->getUrlKey(), ['_query' => $query]);
    }

    /**
     * Processing object after save data
     *
     * @return $this
     */
    public function afterSave()
    {
        // Rewrite url generation
        $this->generateUrls();

        // Save the layout items
        $this->saveLayoutItems();

        return parent::afterSave();
    }

    /**
     * Generate the url rewrite
     *
     * @todo move in url manager model
     * @todo refactor whole method
     * @return $this
     */
    public function generateUrls()
    {
        if (empty($this->getId()) || !$this->hasData(self::URL_KEY)) {
            return $this;
        }
        $urls = [];
        $storeIds = [];

        if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $this->getStores())) {
            foreach ($this->_storeManager->getStores() as $store) {
                $storeIds[] = $store->getId();
            }
            $storeIds = array_unique($storeIds);
        } else {
            $storeIds = $this->getStores();
        }

        foreach ($storeIds as $store) {
            $urls[] = [
                'entity_type' => self::ENTITY_TYPE,
                'entity_id' => $this->getId(),
                'request_path' => $this->getUrlKey(),
                'target_path' => 'contentmanager/index/contentlist/contentlist_id/' . $this->getId(),
                'store_id' => $store,
            ];
        }

        $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY_TYPE, $this->getId());
        $this->_urlRewriteHelper->addUrlRewrites($urls);

        return $this;
    }

    /**
     * Return the stores
     *
     * @return Store[]
     */
    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $stores = $this->getResource()->lookupStoreIds($this->getId());
            if (empty($stores)) {
                $stores = [$this->_storeManager->getStore()->getId()];
            }
            $this->setData('stores', $stores);
        }

        return $this->getData('stores');
    }

    /**
     * Save the custom layout configuration
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    protected function saveLayoutItems()
    {
        // Parent id associated to their real Id
        $groupItem = [];
        // Retrieve layout items
        $data = [];

        if ($this->hasData('after_save_item')) {
            $data = $this->getData('after_save_item');
        }

        // Block, field and group items
        foreach ($this->_availableLayoutItem as $type) {
            // Retrieve items by type
            $items = (isset($data[$type]) && is_array($data[$type])) ? $data[$type] : [];

            // Items
            foreach ($items as $key => $item) {
                $itemModel = $this->_modelFactory->create('\Blackbird\ContentManager\Model\ContentList\Layout\\' . ucfirst(strtolower($type)));

                if ($item['parent_id'] && isset($groupItem[$item['parent_id']])) {
                    $item['parent_uid'] = $groupItem[$item['parent_id']];
                }

                if (!empty($item['id'])) {
                    $itemModel->load($item['id']);

                    if (!empty($itemModel->getId())) {
                        if (!empty($item['is_delete'])) {
                            $itemModel->delete();
                            continue;
                        }
                    } else {
                        // The item no longer exists
                        continue;
                    }
                }
                // If the item is not saved and has been deleted
                if (!empty($item['is_delete'])) {
                    continue;
                }

                // Set data to the item
                $this->setItemData($itemModel, $item, $type);

                try {
                    $itemModel->save();

                    if ($type === 'group') {
                        $groupItem[$key] = $itemModel->getId();
                    }
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Something went wrong while saving the layout item.'));
                }
            }
        }

        return $this;
    }

    /**
     * Set data to an item
     *
     * @param Item $itemModel
     * @param array $item
     * @param string $type
     * @return Item
     */
    protected function setItemData(Item $itemModel, array $item, $type)
    {
        // Set data to the item
        $itemModel->setData($item);
        $itemModel->setData(self::ID, $this->getId());
        // Set id of the item if it exists
        if (!empty($item['id'])) {
            $itemModel->setData($itemModel::ID, $item['id']);
        }

        // Manage format for item type of field
        if ($type === 'field') {
            $itemModel->setFormat($this->getFormatSerialize($item));
            $cfId = (!empty($item['custom_field_id'])) ? $item['custom_field_id'] : null;
            $itemModel->setCustomFieldId($cfId);
        }

        // Set parent layout group item
        if (!empty($item['parent_id'])) {
            $itemModel->setData($itemModel::PARENT_ID, $item['parent_uid']);
        }

        return $itemModel;
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

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        // Delete UrlRewrite
        $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY_TYPE, $this->getId());

        // Delete Layout Items
        $this->deleteLayoutItems();

        return parent::beforeDelete();
    }

    /**
     * Delete the layout items
     *
     * @return $this
     */
    protected function deleteLayoutItems()
    {
        $items = $this->getLayoutItemCollection();
        krsort($items);

        foreach ($items as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Return list of layout items values
     *
     * @return array
     */
    public function getLayoutItemCollection()
    {
        $items = [];

        // Group items
        foreach ($this->getLayoutGroupItemCollection() as $group) {
            $items[$group->getSortOrder()] = $group;
        }

        // Field items
        foreach ($this->getLayoutFieldItemCollection() as $field) {
            $items[$field->getSortOrder()] = $field;
        }

        // Block items
        foreach ($this->getLayoutBlockItemCollection() as $block) {
            $items[$block->getSortOrder()] = $block;
        }

        ksort($items);

        return $items;
    }

    /**
     * Retrieve collection of layout group item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Group\Collection
     */
    public function getLayoutGroupItemCollection()
    {
        return $this->_groupItemCollectionFactory->create()
            ->addFieldToFilter(self::ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);
    }

    /**
     * Retrieve collection of layout field item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Field\Collection
     */
    public function getLayoutFieldItemCollection()
    {
        return $this->_fieldItemCollectionFactory->create()
            ->addFieldToFilter(self::ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);
    }

    /**
     * Retrieve collection of layout block item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Block\Collection
     */
    public function getLayoutBlockItemCollection()
    {
        return $this->_blockItemCollectionFactory->create()
            ->addFieldToFilter(self::ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceContentList::class);
        $this->setIdFieldName(self::ID);
    }
}
