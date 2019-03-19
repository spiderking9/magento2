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
use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ContentType\CustomFieldset;
use Blackbird\ContentManager\Model\ResourceModel\ContentType as ResourceContentType;
use Magento\Eav\Model\Entity\Type;

/**
 * Content type Model
 * @method int getCtId() Get Id of Content Type
 */
class ContentType extends \Blackbird\ContentManager\Model\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentTypeInterface,
    \Blackbird\ContentManager\Api\ContentLayoutInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Content manager content cache tag
     */
    const CACHE_TAG = 'contentmanager_contenttype';

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomFieldset\CollectionFactory
     */
    protected $_customFieldsetCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory
     */
    protected $_customFieldCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\CollectionFactory
     */
    protected $_groupItemCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory
     */
    protected $_fieldItemCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory
     */
    protected $_blockItemCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $_contentListCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var array
     */
    protected $_customFields = [];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomFieldset\CollectionFactory $customFieldsetCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\CollectionFactory $groupItemCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory $fieldItemCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory $blockItemCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\Registry $registry,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomFieldset\CollectionFactory $customFieldsetCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\CollectionFactory $groupItemCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory $fieldItemCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory $blockItemCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_customFieldsetCollectionFactory = $customFieldsetCollectionFactory;
        $this->_customFieldCollectionFactory = $customFieldCollectionFactory;
        $this->_groupItemCollectionFactory = $groupItemCollectionFactory;
        $this->_fieldItemCollectionFactory = $fieldItemCollectionFactory;
        $this->_blockItemCollectionFactory = $blockItemCollectionFactory;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_contentListCollectionFactory = $contentListCollectionFactory;
        $this->_modelFactory = $modelFactory;
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($visibility)
    {
        return $this->setData(self::VISIBILITY, (int)$visibility);
    }

    /**
     * @return bool
     */
    public function isRepeaterField()
    {
        return ($this->getVisibility() === Visibility::REPEATER_FIELD);
    }

    /**
     * @return int
     */
    public function getVisibility()
    {
        return (int)$this->getData(self::VISIBILITY);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return ($this->getVisibility() === Visibility::VISIBLE);
    }

    /**
     * @param array $field
     * @return \Blackbird\ContentManager\Model\ContentType
     */
    public function addCustomField(array $field)
    {
        $this->_customFields[] = $field;

        return $this;
    }

    /**
     * Save all custom fields of the current content type
     *
     * @return $this
     */
    public function saveCustomFields()
    {
        $customFields = $this->getCustomFields();

        foreach ($customFields as $customFieldData) {
            $customField = $this->_modelFactory->create(CustomField::class);

            // Check id of custom field
            if (!empty($customFieldData['option_id'])) {
                $customField->load($customFieldData['option_id']);

                foreach ($customFieldData as $key => $value) {
                    $customField->setData($key, $value);
                }
            } else {
                unset($customFieldData['option_id']);
                $customField->setData($customFieldData)->setData(CustomField::CT_ID, $this->getCtId());
            }

            // Delete custom field if is no more or save
            if ($customField->getData('is_delete') == '1' && !empty($customField->getId())) {
                $customField->delete();
            } else {
                $customField->save();
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return $this->_customFields;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @throws \Magento\Framework\Validator\Exception
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if (strlen($this->getTitle()) < 3) {
            throw new \Magento\Framework\Validator\Exception(__('Attribute \'title\' is less than 3 characters long.'));
        }
        if (strlen($this->getTitle()) > 50) {
            throw new \Magento\Framework\Validator\Exception(__('Attribute \'title\' is more than 50 characters long.'));
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Processing object after save data
     *
     * @return $this
     */
    public function afterSave()
    {
        //$this->saveCustomFields();//todo refactoring in progress

        $this->_getResource()->addCommitCallback([$this, 'reindex']);
        parent::afterSave();
    }

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        // Delete eav attributes
        $this->deleteAttributes();

        // Delete CustomFieldsets (and fields by cascade)
        $this->deleteCustomFieldsets();

        // Delete Layout Items
        $this->deleteLayoutItems();

        // Delete Content Lists
        $this->deleteContentLists();

        // Delete Contents
        $this->deleteContents();

        return $this;
    }

    /**
     * Delete the eav attributes
     *
     * @return $this
     */
    protected function deleteAttributes()
    {
        $entityType = $this->_modelFactory->create(Type::class)->load(Content::ENTITY, 'entity_type_code');
        $customFields = $this->getCustomFieldCollection();
        $attributeCodes = [];

        // Retrieve all attribute code of the current cotnent type
        foreach ($customFields as $customField) {
            $attributeCodes[] = $customField->getIdentifier();

            // Special case for attributes image
            if ($customField->getType() === 'image') {
                $attributeCodes[] = $customField->getIdentifier() . '_orig';
                $attributeCodes[] = $customField->getIdentifier() . '_alt';
                $attributeCodes[] = $customField->getIdentifier() . '_url';
                $attributeCodes[] = $customField->getIdentifier() . '_titl';
            }
        }

        if (!empty($attributeCodes)) {
            // Retrieve all attributes of the current content type
            $attributeCollection = $this->_attributeCollectionFactory->create();
            $attributeCollection->addFieldToFilter('attribute_code', $attributeCodes)
                ->addFieldToFilter('entity_type_id', $entityType->getEntityTypeId());

            foreach ($attributeCollection as $attribute) {
                $attribute->delete();
            }
        }

        return $this;
    }

    /**
     * Retrieve custom fields from a fieldset
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Collection
     */
    public function getCustomFieldCollection()
    {
        $collection = $this->_customFieldCollectionFactory->create()
            ->addFieldToFilter(CustomField::CT_ID, $this->getCtId())
            ->addTitleToResult()
            ->setOrder(CustomField::SORT_ORDER, 'asc')
            ->setOrder(CustomField::FIELDSET_ID, 'asc');

        return $collection;
    }

    /**
     * Delete all Custom Fieldsets of the content type
     *
     * @return $this
     */
    protected function deleteCustomFieldsets()
    {
        foreach ($this->getCustomFieldsetCollection() as $customFieldset) {
            $customFieldset->delete();
        }

        return $this;
    }

    /**
     * Retrieve custom fieldsets from the current content type
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomFieldset\Collection
     */
    public function getCustomFieldsetCollection()
    {
        $collection = $this->_customFieldsetCollectionFactory->create()
            ->addFieldToFilter(CustomFieldset::CT_ID, $this->getCtId())
            ->setOrder(CustomFieldset::SORT_ORDER, 'asc')
            ->setOrder(CustomFieldset::TITLE, 'asc');

        return $collection;
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
        $collection = $this->getLayoutGroupItemCollection();
        foreach ($collection as $group) {
            $items[$group->getSortOrder()] = $group;
        }

        // Field items
        $collection = $this->getLayoutFieldItemCollection();
        foreach ($collection as $field) {
            $items[$field->getSortOrder()] = $field;
        }

        // Block items
        $collection = $this->getLayoutBlockItemCollection();
        foreach ($collection as $block) {
            $items[$block->getSortOrder()] = $block;
        }

        ksort($items);

        return $items;
    }

    /**
     * Retrieve collection of layout group item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\Collection
     */
    public function getLayoutGroupItemCollection()
    {
        $collection = $this->_groupItemCollectionFactory->create()
            ->addFieldToFilter(Item::CT_ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);

        return $collection;
    }

    /**
     * Retrieve collection of layout field item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\Collection
     */
    public function getLayoutFieldItemCollection()
    {
        $collection = $this->_fieldItemCollectionFactory->create()
            ->addFieldToFilter(Item::CT_ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);

        return $collection;
    }

    /**
     * Retrieve collection of layout block item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\Collection
     */
    public function getLayoutBlockItemCollection()
    {
        $collection = $this->_blockItemCollectionFactory->create()
            ->addFieldToFilter(Item::CT_ID, $this->getId())
            ->setOrder(Item::SORT_ORDER);

        return $collection;
    }

    /**
     * Delete all Content lists which refer to this content type
     *
     * @return $this
     */
    protected function deleteContentLists()
    {
        foreach ($this->getContentListCollection() as $contentList) {
            $contentList->delete();
        }

        return $this;
    }


    /**
     * Retrieve the content list collection
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\Collection
     */
    public function getContentListCollection()
    {
        return $this->_contentListCollectionFactory->create()->addFieldToFilter(ContentList::ID, $this->getId());
    }

    /**
     * Delete all Contents which refer to this content type
     *
     * @return $this
     */
    protected function deleteContents()
    {
        foreach ($this->getContentCollection() as $content) {
            $content->delete();
        }

        return $this;
    }

    /**
     * Retrieve the content collection
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     */
    public function getContentCollection()
    {
        return $this->_contentCollectionFactory->create()->addContentTypeFilter($this->getId());
    }

    /**
     * Init indexing process after content delete
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDeleteCommit()
    {
        $this->reindex();

        return parent::afterDeleteCommit();
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
            $contentIds = $this->getContentCollection()->getAllIds();
            if (!empty($contentIds)) {
                $indexer->reindexList($this->getContentCollection()->getAllIds());
            }
        }
    }

    /**
     * List of main attributes codes
     *
     * @return string[]
     * @deprecated
     */
    public function getMainAttributes()
    {
        return [
            'title',
            'use_default_title',
            'status',
            'url_key',
            'meta_title',
            'use_default_meta_title',
            'description',
            'use_default_description',
            'keywords',
            'use_default_keywords',
            'robots',
            'og_title',
            'use_default_og_title',
            'og_description',
            'use_default_og_description',
            'og_url',
            'use_default_og_url',
            'og_type',
            'use_default_og_type',
            'og_image',
            'use_default_og_image',
        ];
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceContentType::class);
        $this->setIdFieldName(self::ID);
    }
}
