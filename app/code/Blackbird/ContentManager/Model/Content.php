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

use Blackbird\ContentManager\Api\Data\ContentInterface;
use Blackbird\ContentManager\Api\Data\ContentInterfaceFactory;
use Blackbird\ContentManager\Api\Data\ContentType\Layout\BlockInterface;
use Blackbird\ContentManager\Api\Data\ContentType\Layout\FieldInterface;
use Blackbird\ContentManager\Api\Data\ContentType\Layout\GroupInterface;
use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ContentType\CustomField\Option;
use Blackbird\ContentManager\Model\ResourceModel\Content as ResourceContent;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Model
 * @method void setCtId() Set Id of the Content Type
 */
class Content extends \Blackbird\ContentManager\Model\AbstractModel implements ContentInterface, IdentityInterface
{
    /**
     * Entity code
     */
    const ENTITY = 'contenttype_content';

    /**
     * Content manager content cache tag
     */
    const CACHE_TAG = 'contentmanager_content';

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'contenttype_content';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'contenttype_content';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlManager;

    /**
     * @var \Blackbird\ContentManager\Api\Data\ContentInterfaceFactory
     */
    protected $contentDataFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Blackbird\ContentManager\Api\ContentMetadataInterface
     */
    protected $metadataService;

    /**
     * @var \Blackbird\ContentManager\Model\ContentType
     */
    protected $contentType;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Blackbird\ContentManager\Helper\UrlRewriteGenerator
     */
    protected $_urlRewriteHelper;

    /**
     * @var \Blackbird\ContentManager\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlManager
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Blackbird\ContentManager\Api\Data\ContentInterfaceFactory $contentDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Blackbird\ContentManager\Api\ContentMetadataInterface $metadataService
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper
     * @param \Blackbird\ContentManager\Helper\Image $imageHelper
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlManager,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content $resource,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        ContentInterfaceFactory $contentDataFactory,
        DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Blackbird\ContentManager\Api\ContentMetadataInterface $metadataService,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        AttributeRepositoryInterface $attributeRepository,
        \Blackbird\ContentManager\Helper\UrlRewriteGenerator $urlRewriteHelper,
        \Blackbird\ContentManager\Helper\Image $imageHelper,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->metadataService = $metadataService;
        $this->_modelFactory = $modelFactory;
        $this->_storeManager = $storeManager;
        $this->_urlManager = $urlManager;
        $this->contentDataFactory = $contentDataFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->indexerRegistry = $indexerRegistry;
        $this->_blockFactory = $blockFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->attributeRepository = $attributeRepository;
        $this->_urlRewriteHelper = $urlRewriteHelper;
        $this->_imageHelper = $imageHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [self::CACHE_TAG . '_' . $this->getId()];

        if ($this->getCtId()) {
            $identities[] = ContentType::CACHE_TAG . '_' . $this->getContentType()->getId();
        }

        return $identities;
    }

    /**
     * Return the linked Content Type
     *
     * @return \Blackbird\ContentManager\Model\ContentType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentType()
    {
        if (!$this->hasData('content_type') && $this->getCtId()) {
            $this->setData('content_type', $this->_modelFactory->create(ContentType::class)->load($this->getCtId()));
        }

        return $this->getData('content_type');
    }

    /**
     * Retrieve store where content was created
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->getStoreId());
    }

    /**
     * Get the available stores for this content
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $this->setData('stores', $this->getResource()->getAvailableStores($this->getId()));
        }

        return $this->getData('stores');
    }

    /**
     * Retrieve all store views id of the content
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasData('store_ids')) {
            $this->setData('store_ids', $this->getResource()->lookupStoreIds($this->getId()));
        }

        return $this->getData('store_ids');
    }

    /**
     * Set store to content
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore(\Magento\Store\Model\Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());

        return $this;
    }

    /**
     * Check if a store exists for this content
     *
     * @param int $storeId
     * @return boolean
     */
    public function existsForStore($storeId)
    {
        return $this->getResource()->existsForStore($this->getId(), $storeId);
    }

    /**
     * Retrieve the url (UrlKey as Request Path)
     *
     * @todo retrieve the request path (url rewrite)
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlKey();
    }

    /**
     * Retrieve the current content url
     *
     * @todo rename to getContentUrl (discuss)
     * @todo refactor
     * @param Store|int|string $store
     * @param bool $preview
     * @return string
     */
    public function getLinkUrl($store = null, $preview = false)
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
     * {@inheritdoc}
     */
    public function afterSave()
    {
        parent::afterSave();

        if (!$this->getData('ignore_generate_urls')) {
            // Rewrite url generation
            $this->generateUrls();
        }

        // Fulltext indexer
        $this->_getResource()->addCommitCallback([$this, 'reindex']);

        return $this;
    }

    /**
     * Generate the url rewrite
     *
     * @todo move in url manager model
     * @todo refactor whole method
     * @return $this
     * @throws \Exception
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    public function generateUrls()
    {
        if (empty($this->getId()) || !$this->hasData(self::URL_KEY)) {
            return $this;
        }
        $urls = [];

        if (!$this->getContentType()->isVisible()) {
            $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY, $this->getId());
        } elseif ($this->getStoreId() == Store::DEFAULT_STORE_ID) {
            $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY, $this->getId());

            if ($this->isObjectCopied()) {
                foreach ($this->_storeManager->getStores() as $store) {
                    $urls[] = [
                        'entity_type' => self::ENTITY,
                        'entity_id' => $this->getId(),
                        'request_path' => $this->getUrlKey(),
                        'target_path' => 'contentmanager/index/content/content_id/' . $this->getId(),
                        'store_id' => $store->getId(),
                    ];
                }
            } else {
                // todo refactor, do not modify current object instance
                $oldStoreId = $this->getStoreId();
                foreach ($this->_storeManager->getStores() as $store) {
                    $this->setStoreId($store->getId());
                    $this->getResource()->load($this, $this->getId());

                    $urls[] = [
                        'entity_type' => self::ENTITY,
                        'entity_id' => $this->getId(),
                        'request_path' => $this->getUrlKey(),
                        'target_path' => 'contentmanager/index/content/content_id/' . $this->getId(),
                        'store_id' => $this->getStoreId(),
                    ];
                }
                $this->setStoreId($oldStoreId);
                $this->getResource()->load($this, $this->getId());
            }
        } else {
            $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY, $this->getId(), $this->getStoreId());
            $urls[] = [
                'entity_type' => self::ENTITY,
                'entity_id' => $this->getId(),
                'request_path' => $this->getUrlKey(),
                'target_path' => 'contentmanager/index/content/content_id/' . $this->getId(),
                'store_id' => $this->getStoreId(),
            ];
        }

        $this->_urlRewriteHelper->addUrlRewrites($urls);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        // Delete UrlRewrite
        $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY, $this->getId());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        parent::afterDeleteCommit();

        $this->reindex();

        return $this;
    }

    /**
     * Init indexing process after content save
     *
     * @return void
     */
    public function reindex()
    {
        /** @var \Magento\Framework\Indexer\IndexerInterface $indexer */
        $indexer = $this->indexerRegistry->get(Indexer\Fulltext::INDEXER_ID);
        if (!$indexer->isScheduled()) {
            $indexer->reindexRow($this->getId());
        }
    }

    /**
     * Get content created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return (new \DateTime($date))->getTimestamp();
        }

        return null;
    }

    /**
     * Return Entity Type Id value
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if (!$this->hasData(self::ENTITY_TYPE_ID)) {
            $this->setData(self::ENTITY_TYPE_ID, $this->getEntityType()->getId());
        }

        return $this->getData(self::ENTITY_TYPE_ID);
    }

    /**
     * Return Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    public function getEntityType()
    {
        return $this->getResource()->getEntityType();
    }

    /**
     * Delete values for the current store
     *
     * @return void
     */
    public function deleteCurrentStoreAttributes()
    {
        $attributeIds = [];

        foreach ($this->getData() as $key => $value) {
            if (!in_array($key, $this->_protectedAttributes())) {
                $attribute = $this->getAttribute($key);
                if ($attribute) {
                    $attributeIds[] = $attribute->getId();
                }
            }
        }

        $this->getResource()->deleteAttributesByStore($this->getId(), $this->getStoreId(), $attributeIds);
        // Delete UrlRewrite for the current store
        $this->_urlRewriteHelper->deleteUrlRewrite(self::ENTITY, $this->getId(), $this->getStoreId());
    }

    /**
     * Return the protected attributes
     *
     * @return array
     */
    protected function _protectedAttributes()
    {
        return [
            self::ID,
            self::ENTITY_TYPE_ID,
            self::CT_ID,
            self::CREATED_AT,
            self::UPDATED_AT,
            self::STORE_ID,
        ];
    }

    /**
     * Get content attribute model object
     *
     * @param string $attributeCode
     * @return Attribute|null
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();

        return $attributes[$attributeCode] ?? null;
    }

    /**
     * Retrieve all content attributes
     *
     * @return Attribute[]
     */
    public function getAttributes()
    {
        if (!$this->hasData('attributes')) {
            $this->setData('attributes', $this->getResource()->loadAllAttributes($this)->getSortedAttributes());
        }

        return $this->getData('attributes');
    }

    /**
     * Render anything
     *
     * @param mixed $element the element to render
     * @param array $params extra parameters
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @api
     */
    public function render($element, $params = null)
    {
        $customField = null;
        $isPageTitle = false;
        $layout = null;
        $html = '';

        // Is identifier
        if (is_string($element)) {
            $customField = $this->getContentType()
                ->getCustomFieldCollection()
                ->addFieldToFilter(CustomField::IDENTIFIER, $element)
                ->getFirstItem();

            $identifier = $element;

            // Is custom field model
        } elseif ($element instanceof CustomField) {
            $customField = $this->getContentType()
                ->getCustomFieldCollection()
                ->addFieldToFilter(CustomField::IDENTIFIER, $element->getIdentifier())
                ->getFirstItem();

            $identifier = $element->getIdentifier();

            // Is layout field model
        } elseif ($element instanceof FieldInterface) {
            if (!$element->getCustomFieldId()) {
                $isPageTitle = true;
                $identifier = 'title';
            } else {
                $customField = $this->getContentType()
                    ->getCustomFieldCollection()
                    ->addFieldToFilter('main_table.' . CustomField::ID, $element->getCustomFieldId())
                    ->getFirstItem();

                $identifier = $customField->getIdentifier();
            }
            $layout = $element;

            // Is layout block model
        } elseif ($element instanceof BlockInterface) {
            $html = $this->renderLayoutBlock($element, ['params' => $params]);

            // Is layout block model
        } elseif ($element instanceof GroupInterface) {
            $html = $this->renderLayoutGroup($element, ['params' => $params]);
        }

        // Render custom field
        if ($customField || $isPageTitle) {
            $html = $this->renderCustomField($identifier, [
                'custom_field' => $customField,
                'params' => $params,
                'layout' => $layout,
            ]);
        }

        return $html;
    }

    /**
     * Render a layout cms block
     *
     * @param BlockInterface $layoutBlock
     * @param string|array $params
     * @return string
     */
    public function renderLayoutBlock(BlockInterface $layoutBlock, $params)
    {
        $block = $this->_blockFactory->createBlock(\Blackbird\ContentManager\Block\View\Block::class)->setData([
            'layout_block' => $layoutBlock,
            'content' => $this,
            'params' => $params,
        ]);

        return $block->prepareTemplate()->toHtml();
    }

    /**
     * Render a group of layout items
     *
     * @param GroupInterface $layoutGroup
     * @param string|array $params
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderLayoutGroup(GroupInterface $layoutGroup, $params)
    {
        // Render header
        $block = $this->_blockFactory->createBlock(\Blackbird\ContentManager\Block\View\Group\Header::class)->setData([
            'layout_group' => $layoutGroup,
            'content' => $this,
            'params' => $params,
        ]);

        $result = $block->prepareTemplate()->toHtml();

        // Render children
        foreach ($layoutGroup->getChildren() as $layoutChild) {
            $result .= $this->render($layoutChild, $params['params'] ?? null);
        }

        // Render footer
        $block = $this->_blockFactory->createBlock(\Blackbird\ContentManager\Block\View\Group\Footer::class)->setData([
            'layout_group' => $layoutGroup,
            'content' => $this,
            'params' => $params,
        ]);

        return $result . $block->prepareTemplate()->toHtml();
    }

    /**
     * Render a custom field
     *
     * @param string $identifier
     * @param string|array $params
     * @return string
     */
    public function renderCustomField($identifier, $params)
    {
        if (!empty($params['custom_field'])) {
            $customField = $params['custom_field'];
            $type = $customField->getType();
        }

        $block = $this->_blockFactory->createBlock(\Blackbird\ContentManager\Block\View\Field::class)->setData([
            'identifier' => $identifier,
            'type' => $type ?? 'field',
            'content' => $this,
            'params' => $params,
        ]);

        return $block->prepareTemplate()->toHtml();
    }

    /**
     * Get the url of the given file identifier
     *
     * @param string $identifier
     * @return string
     */
    public function getFile($identifier)
    {
        return $this->retrieveFileUrl($identifier, ContentType::CT_FILE_FOLDER);
    }

    /**
     * Retrieve an url for a given identifier file and path
     *
     * @param string $identifier
     * @param string $path
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function retrieveFileUrl($identifier, $path = '')
    {
        $customField = $this->getContentType()->getCustomFieldCollection()
            ->addFieldToFilter(CustomField::IDENTIFIER, $identifier)
            ->addFieldToFilter(CustomField::TYPE, ['image', 'file'])
            ->getFirstItem();

        if ($customField && !empty($customField->getFilePath())) {
            $path .= $customField->getFilePath();

            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
        }
        $path .= $this->getData($identifier);

        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }

    /**
     * Get the url of the given image identifier
     *
     * @param string $identifier
     * @param int $width
     * @param int $height
     * @param bool $keepAspectRatio
     * @param bool $cropped
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImage($identifier, $width = null, $height = null, $keepAspectRatio = false, $cropped = true)
    {
        // Workaround: if 0, force to null
        $width = $width ?: null;
        $height = $height ?: null;

        $imageField = $this->getContentType()
            ->getCustomFieldCollection()
            ->addFieldToFilter(CustomField::IDENTIFIER, $identifier)
            ->addFieldToFilter(CustomField::TYPE, 'image')
            ->getFirstItem();

        if (empty($width) && empty($height)) {
            return $this->getUnmodifiedImage($imageField, $cropped);
        }

        return $this->getModifiedImage($imageField, $width, $height, $keepAspectRatio, $cropped);
    }

    /**
     * Retrieve the url of the image (resized)
     *
     * @param CustomField $imageField
     * @param int $width
     * @param int $height
     * @param bool $forceKeepAspectRatio
     * @param bool $cropped
     * @return string
     */
    protected function getModifiedImage(
        CustomField $imageField,
        $width = null,
        $height = null,
        $forceKeepAspectRatio = false,
        $cropped = true
    ) {
        $path = ($cropped && $imageField->getCrop()) ? ContentType::CT_IMAGE_CROPPED_FOLDER : '';

        $file = $this->getData($imageField->getIdentifier());
        $keepAspectRatio = is_bool($forceKeepAspectRatio) ? $forceKeepAspectRatio : $imageField->getKeepAspectRatio();
        if (!empty($imageField->getFilePath())) {
            $path .= $imageField->getFilePath();
        }

        $this->_imageHelper->init($file, $path);

        return $this->_imageHelper->resize($width, $height, $keepAspectRatio);
    }

    /**
     * Retrieve the url of the image (original size)
     *
     * @param CustomField $imageField
     * @param bool $cropped
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getUnmodifiedImage(CustomField $imageField, $cropped = true)
    {
        $identifier = $imageField->getIdentifier();
        $path = ContentType::CT_FILE_FOLDER;

        if ($cropped && $imageField->getCrop()) {
            $path .= ContentType::CT_IMAGE_CROPPED_FOLDER;
        }

        return $this->retrieveFileUrl($identifier, $path) ?: $this->getImage($identifier);
    }

    /**
     * Retrieve the associative text for an attribute value
     *
     * @todo check: used the backend attribute Array for attributes
     * @param string $identifier Attribute identifier
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeText($identifier)
    {
        $result = $this->getData($identifier);
        /** @var CustomField $customField */
        $customField = $this->getContentType()->getCustomFieldCollection()
            ->addFieldToFilter(CustomField::IDENTIFIER, $identifier)
            ->getFirstItem();

        // If attribute is type of select or product attribute
        if ($customField && in_array($customField->getType(),
                ['drop_down', 'radio', 'multiple', 'checkbox', 'attribute', 'currency', 'locale',])) {
            $result = [];
            $values = explode(',', $this->getData($identifier));

            if ($customField->getType() === 'attribute') {
                /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
                $attribute = $this->attributeRepository->get(
                    Product::ENTITY,
                    $customField->getData(CustomField::ATTRIBUTE)
                );

                foreach ($values as $value) {
                    $result[] = $attribute->getSource()->getOptionText($value);
                }
            } else {
                $options = $customField->getOptionCollection()->addFieldToFilter(Option::VALUE, $values);
                /** @var Option $option */
                foreach ($options as $option) {
                    $result[] = (!empty($option->getTitle())) ? __($option->getTitle()) : $option->getValue();
                }
            }

            $result = implode(',', $result);
        }

        return $result;
    }

    /**
     * Retrieve the product collection of a product field
     *
     * @param string $identifier
     * @param string|array $attributes
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection($identifier, $attributes = [])
    {
        return $this->_productCollectionFactory->create()
            ->addAttributeToSelect($attributes)
            ->addAttributeToFilter(Product::SKU, ['in' => $this->getDataAsArray($identifier)]);
    }

    /**
     * Retrieve the data as an array
     *
     * @param string $identifier
     * @return array
     */
    public function getDataAsArray($identifier)
    {
        $value = $this->getData($identifier);
        return $value ? array_filter(array_map('trim', explode(',', $value))) : [];
    }

    /**
     * Retrieve the content collection of a content field
     *
     * @param string $identifier
     * @param string|array $attributes
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     */
    public function getContentCollection($identifier, $attributes = [])
    {
        return $this->getCollection()
            ->addAttributeToSelect($attributes)
            ->addAttributeToFilter(self::ID, ['in' => $this->getDataAsArray($identifier)]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceContent::class);
        $this->setIdFieldName(self::ID);

        parent::_construct();
    }
}
