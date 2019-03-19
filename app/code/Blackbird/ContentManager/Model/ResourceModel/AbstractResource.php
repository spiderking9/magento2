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

namespace Blackbird\ContentManager\Model\ResourceModel;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Content entity abstract model
 *
 * Class AbstractResource
 *
 * @package Blackbird\ContentManager\Model\ResourceModel
 */
abstract class AbstractResource extends \Magento\Eav\Model\Entity\AbstractEntity
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Model factory
     *
     * @var \Blackbird\ContentManager\Model\Factory
     */
    protected $_modelFactory;

    /**
     * AbstractResource constructor
     *
     * @param \Magento\Eav\Model\Entity\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve attribute's raw value from DB.
     *
     * @param int $entityId
     * @param int|string|array $attribute attribute's ids or codes
     * @param int|\Magento\Store\Model\Store $store
     * @return bool|string|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeRawValue($entityId, $attribute, $store)
    {
        $attributesData = [];
        $staticAttributes = [];
        $typedAttributes = [];
        $staticTable = null;
        $connection = $this->getConnection();
        if (!is_array($attribute)) {
            $attribute = [$attribute];
        }
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = $store->getId();
        }
        $store = (int)$store;

        // Check provided data
        if (!$entityId || empty($attribute)) {
            return false;
        }

        foreach ($attribute as $item) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $item = $this->getAttribute($item);
            if (!$item) {
                continue;
            }
            $attributeCode = $item->getAttributeCode();
            $attrTable = $item->getBackend()->getTable();

            if ($item->getBackend()->isStatic()) {
                $staticAttributes[] = $attributeCode;
                $staticTable = $attrTable;
            } else {
                // That structure needed to avoid farther sql joins for getting attribute's code by id
                $typedAttributes[$attrTable][$item->getId()] = $attributeCode;
            }
        }

        // Collecting static attributes
        if ($staticAttributes) {
            $select = $connection->select()
                ->from($staticTable, $staticAttributes)
                ->join(['e' => $this->getEntityTable()],
                    'e.' . $this->getLinkField() . ' = ' . $staticTable . '.' . $this->getLinkField())
                ->where('e.entity_id = :entity_id');
            $attributesData = $connection->fetchRow($select, ['entity_id' => $entityId]);
        }

        // Collecting typed attributes, performing separate SQL query for each attribute type table
        if ($typedAttributes) {
            foreach ($typedAttributes as $table => $_attributes) {
                $select = $connection->select()
                    ->from(['default_value' => $table], ['attribute_id'])
                    ->join(['e' => $this->getEntityTable()],
                        'e.' . $this->getLinkField() . ' = ' . 'default_value.' . $this->getLinkField(), '')
                    ->where('default_value.attribute_id IN (?)', array_keys($_attributes))
                    ->where("e.entity_id = :entity_id")
                    ->where('default_value.store_id = ?', 0);
                $bind = ['entity_id' => $entityId];

                if ($store != $this->getDefaultStoreId()) {
                    $joinCondition = [
                        $connection->quoteInto('store_value.attribute_id IN (?)', array_keys($_attributes)),
                        "store_value.{$this->getLinkField()} = e.{$this->getLinkField()}",
                        'store_value.store_id = :store_id',
                    ];
                    $valueExpr = $connection->getCheckSql('store_value.value IS NULL', 'default_value.value',
                        'store_value.value');
                    $bind['store_id'] = $store;

                    $select->joinLeft(['store_value' => $table], implode(' AND ', $joinCondition),
                        ['attr_value' => $valueExpr]);
                } else {
                    $select->columns(['attr_value' => 'value'], 'default_value');
                }

                $result = $connection->fetchPairs($select, $bind);
                foreach ($result as $attrId => $value) {
                    $attributesData[$typedAttributes[$table][$attrId]] = $value;
                }
            }
        }

        if (sizeof($attributesData) == 1) {
            $_data = each($attributesData);
            $attributesData = $_data[1];
        }

        return $attributesData ?: false;
    }

    /**
     * Returns default Store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * Re-declare attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return \Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute::class;
    }

    /**
     * Retrieve select object for loading entity attributes values
     * Join attribute store value
     *
     * @todo AttributeSet (future feature)
     * @param \Magento\Framework\DataObject $object
     * @param string $table
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        $storeIds = [$this->getDefaultStoreId()];

        // Retrieve the store id
        if ($this->_storeManager->hasSingleStore()) {
            $storeId = (int)$this->_storeManager->getStore(true)->getId();
        } else {
            $storeId = (int)$object->getStoreId();
        }
        // Prepare stores ids
        if ($storeId != $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['attr_table' => $table], [])
            ->where("attr_table.{$this->getLinkField()} = ?", $object->getData($this->getLinkField()))
            ->where('attr_table.store_id IN (?)', $storeIds);

        return $select;
    }

    /**
     * Prepare select object for loading entity attributes values
     *
     * @param array $selects
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareLoadSelect(array $selects)
    {
        $select = parent::_prepareLoadSelect($selects);
        $select->order('store_id');

        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param \Blackbird\ContentManager\Model\AbstractModel $object
     * @param array $valueRow
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setAttributeValue($object, $valueRow)
    {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isDefaultStore = $valueRow['store_id'] == $this->getDefaultStoreId();
            if (isset($this->_attributes[$valueRow['attribute_id']])) {
                if ($isDefaultStore) {
                    $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                } else {
                    $object->setAttributeDefaultValue($attributeCode,
                        $this->_attributes[$valueRow['attribute_id']]['value']);
                }
            } else {
                $this->_attributes[$valueRow['attribute_id']] = $valueRow;
            }

            $value = $valueRow['value'];
            $valueId = $valueRow['value_id'];

            $object->setData($attributeCode, $value);
            if (!$isDefaultStore) {
                $object->setExistsStoreValueFlag($attributeCode);
            }
            $attribute->getBackend()->setEntityValueId($object, $valueId);
        }

        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * @param \Magento\Framework\DataObject $object
     * @param AbstractAttribute $attribute
     * @param mixed $value
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        $storeId = $this->_storeManager->getStore($object->getStoreId())->getId();

        // Save required attributes in global scope every time if store id different from default
        if ($this->getDefaultStoreId() != $storeId && ($attribute->getIsRequired() || $attribute->getIsRequiredInAdminStore())) {
            $table = $attribute->getBackend()->getTable();

            if (!$this->attributeValueExists($object->getId(), $attribute, $storeId)) {
                $bind = $this->_prepareDataForTable(new \Magento\Framework\DataObject([
                    'attribute_id' => $attribute->getAttributeId(),
                    'store_id' => $this->getDefaultStoreId(),
                    'entity_id' => $object->getEntityId(),
                    'value' => $this->_prepareValueForSave($value, $attribute),
                ]), $table);

                $this->getConnection()->insertOnDuplicate($table, $bind, ['value']);
            }
        }

        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    /**
     * Check if the attribute value exists for the given store id
     *
     * @param int $entityId
     * @param object $attribute
     * @param int $storeId
     * @return boolean
     */
    protected function attributeValueExists($entityId, $attribute, $storeId)
    {
        return !empty($this->getValueId($entityId, $attribute, $storeId));
    }

    /**
     * Retrieve the attribute value id for the given store
     *
     * @param int $entityId
     * @param object $attribute
     * @param int $storeId
     * @return int
     */
    protected function getValueId($entityId, $attribute, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($attribute->getBackend()->getTable(), 'value_id')
            ->where($attribute->getBackend()->getEntityIdField() . ' = :entity_field_id')
            ->where('store_id = :store_id')
            ->where('attribute_id = :attribute_id');
        $bind = [
            'entity_field_id' => $entityId,
            'store_id' => $storeId,
            'attribute_id' => $attribute->getId(),
        ];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Insert or Update attribute data
     *
     * @param \Blackbird\ContentManager\Model\AbstractModel $object
     * @param AbstractAttribute $attribute
     * @param mixed $value
     * @return $this
     */
    protected function _saveAttributeValue($object, $attribute, $value)
    {
        $connection = $this->getConnection();
        $storeId = (int)$this->_storeManager->getStore($object->getStoreId())->getId();
        $table = $attribute->getBackend()->getTable();
        $bind = $this->_prepareDataForTable(new \Magento\Framework\DataObject([
            'attribute_id' => $attribute->getAttributeId(),
            'store_id' => $storeId,
            'entity_id' => $object->getEntityId(),
            'value' => $this->_prepareValueForSave($value, $attribute),
        ]), $table);

        // If it's a single store mode, all values should be saved for only the default store id
        if ($this->_storeManager->hasSingleStore()) {
            $connection->delete($table, [
                'attribute_id = ?' => $attribute->getAttributeId(),
                'entity_id = ?' => $object->getEntityId(),
                'store_id <> ?' => $this->getDefaultStoreId(),
            ]);
        }

        // Init attribute values to save by table
        if (!isset($this->_attributeValuesToSave[$table])) {
            $this->_attributeValuesToSave[$table] = [];
        }

        if ($attribute->isScopeStore()) {
            // Update attribute value for store
            $this->_attributeValuesToSave[$table][] = $bind;
        } elseif ($attribute->isScopeWebsite() && $storeId != $this->getDefaultStoreId()) {
            // Update attribute value for website
            $storeIds = $this->_storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $bind['store_id'] = (int)$storeId;
                $this->_attributeValuesToSave[$table][] = $bind;
            }
        } else {
            // Update global attribute value
            $bind['store_id'] = $this->getDefaultStoreId();
            $this->_attributeValuesToSave[$table][] = $bind;
        }

        return $this;
    }

    /**
     * Update entity attribute value
     *
     * @param \Magento\Framework\DataObject $object
     * @param AbstractAttribute $attribute
     * @param mixed $valueId
     * @param mixed $value
     * @return $this
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        return $this->_saveAttributeValue($object, $attribute, $value);
    }

    /**
     * Update attribute value for specific store
     *
     * @param \Blackbird\ContentManager\Model\AbstractModel $object
     * @param object $attribute
     * @param mixed $value
     * @param int $storeId
     * @return $this
     */
    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $connection = $this->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $valueId = $this->getValueId($object->getId(), $attribute, $storeId);
        $bindValue = $this->_prepareValueForSave($value, $attribute);

        // If value for store exist
        if ($valueId) {
            $connection->update($table, ['value' => $bindValue], ['value_id = ?' => (int)$valueId]);
        } else {
            $connection->insert($table, [
                $entityIdField => (int)$object->getId(),
                'attribute_id' => (int)$attribute->getId(),
                'value' => $bindValue,
                'store_id' => (int)$storeId,
            ]);
        }

        return $this;
    }

    /**
     * Delete entity attribute values
     *
     * @param \Magento\Framework\DataObject $object
     * @param string $table
     * @param array $info
     * @return $this
     */
    protected function _deleteAttributes($object, $table, $info)
    {
        $connection = $this->getConnection();
        $entityIdField = $this->getLinkField();
        $globalValues = [];
        $websiteAttributes = [];
        $storeAttributes = [];
        $condition = [$entityIdField . ' = ?' => $object->getId()];

        // Separate attributes by scope
        foreach ($info as $itemData) {
            $attribute = $this->getAttribute($itemData['attribute_id']);

            if ($attribute->isScopeStore()) {
                $storeAttributes[] = (int)$itemData['attribute_id'];
            } elseif ($attribute->isScopeWebsite()) {
                $websiteAttributes[] = (int)$itemData['attribute_id'];
            } elseif ($itemData['value_id'] !== null) {
                $globalValues[] = (int)$itemData['value_id'];
            }
        }

        // Delete global scope attributes
        if (!empty($globalValues)) {
            $connection->delete($table, ['value_id IN (?)' => $globalValues]);
        }

        // Delete website scope attributes
        if (!empty($websiteAttributes)) {
            $storeIds = $object->getWebsiteStoreIds();
            if (!empty($storeIds)) {
                $delCondition = array_merge($condition, [
                    'attribute_id IN(?)' => $websiteAttributes,
                    'store_id IN(?)' => $storeIds,
                ]);

                $connection->delete($table, $delCondition);
            }
        }

        // Delete store scope attributes
        if (!empty($storeAttributes)) {
            $delCondition = array_merge($condition, [
                'attribute_id IN(?)' => $storeAttributes,
                'store_id IN(?)' => (int)$object->getStoreId(),
            ]);

            $connection->delete($table, $delCondition);
        }

        return $this;
    }

    /**
     * Retrieve Object instance with original data
     *
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Framework\DataObject|\Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getOrigObject($object)
    {
        $className = get_class($object);
        $origObject = $this->_modelFactory->create($className);
        $origObject->unsetData();
        $origObject->setStoreId($object->getStoreId());
        $this->load($origObject, $object->getData($this->getEntityIdField()));

        return $origObject;
    }

    /**
     * Return if attribute exists in original data array.
     * Checks also attribute's store scope
     *
     * @param AbstractAttribute $attribute
     * @param mixed $value New value of the attribute.
     * @param array &$origData
     * @return bool
     */
    protected function _canUpdateAttribute(AbstractAttribute $attribute, $value, array &$origData)
    {
        $result = parent::_canUpdateAttribute($attribute, $value, $origData);
        $result = ($result && ($attribute->isScopeStore() || $attribute->isScopeWebsite()));

        return $result;
    }
}
