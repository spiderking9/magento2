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

use Blackbird\ContentManager\Api\Data\ContentListInterface;

/**
 * Content List Resource Model
 *
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Model\ResourceModel
 */
class ContentList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
    implements \Blackbird\ContentManager\Api\Data\ContentListInterface
{
    /**
     * @var \Magento\Framework\Filter\Factory
     */
    protected $_filterFactory;

    /**
     * @var string
     */
    protected $_contentListStoreTable = '';

    /**
     * ContentList constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Filter\Factory $filterFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Filter\Factory $filterFactory,
        $connectionName = null
    ) {
        $this->_filterFactory = $filterFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blackbird_contenttype_list', ContentListInterface::ID);
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $contentList
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $contentList)
    {
        // Load content list stores
        if ($contentList->getId()) {
            $stores = $this->lookupStoreIds($contentList->getId());

            $contentList->setData('stores', $stores);
        }

        parent::_afterLoad($contentList);

        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $contentListId
     * @return array
     */
    public function lookupStoreIds($contentListId)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getContentListStoreTable(), 'store_id')
            ->where(self::ID . ' = ?', (int)$contentListId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get entity store table name
     *
     * @return string
     */
    public function getContentListStoreTable()
    {
        if (empty($this->_contentListStoreTable)) {
            $this->_contentListStoreTable = $this->getTable('blackbird_contenttype_list_store');
        }

        return $this->_contentListStoreTable;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $contentList
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $contentList)
    {
        $this->manageURLKey($contentList);

        parent::_beforeSave($contentList);

        return $this;
    }

    /**
     * Check wheter content list URL key is correct
     *
     * @param \Magento\Framework\Model\AbstractModel $contentList
     * @throws \Magento\Framework\Exception\LocalizedException
     * @internal param \Magento\Framework\Model\AbstractModel $object
     */
    protected function manageURLKey(\Magento\Framework\Model\AbstractModel $contentList)
    {
        if (!$contentList->hasData(self::URL_KEY) || empty($contentList->getData(self::URL_KEY))) {
            $contentList->setData(self::URL_KEY, $this->formatUrlKey($contentList->getTitle()));
        }

        if (!$this->isValidURLKey($contentList)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericURLKey($contentList)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The page URL key cannot be made of only numbers.'));
        }
    }

    /**
     * Format the string as an url string
     *
     * @param string $str
     * @return string
     * @throws \Zend_Filter_Exception
     */
    public function formatUrlKey($str)
    {
        $removeAccents = $this->_filterFactory->createFilter('removeAccents');
        $str = $removeAccents->filter($str);
        $str = preg_replace('#[^0-9a-z/\.]+#i', '-', $str);
        $str = strtolower($str);
        $str = trim($str, '-');

        return $str;
    }

    /**
     * Check whether content list URL key is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidURLKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData(self::URL_KEY));
    }

    /**
     * Check whether content list URL key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericURLKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData(self::URL_KEY));
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $contentList)
    {
        // Update the stores
        $this->_updateStores($contentList);

        parent::_afterSave($contentList);

        return $this;
    }

    /**
     * Update the content list stores
     *
     * @param \Magento\Framework\Model\AbstractModel $contentList
     */
    protected function _updateStores(\Magento\Framework\Model\AbstractModel $contentList)
    {
        $oldStores = $this->lookupStoreIds($contentList->getId());
        $newStores = $contentList->getStores();

        $toInsert = array_diff($newStores, $oldStores);
        $toDelete = array_diff($oldStores, $newStores);

        $contentListId = $contentList->getId();
        $contentListTable = $this->getContentListStoreTable();

        // Delete case
        if (!empty($toDelete)) {
            $where = [
                self::ID . ' = ?' => $contentListId,
                self::STORE_ID . ' IN (?)' => $toDelete,
            ];
            $this->getConnection()->delete($contentListTable, $where);
        }
        // Insert case
        if (!empty($toInsert)) {
            $toInsert = $this->associatedValues($toInsert, $contentListId, self::STORE_ID, self::ID);
            $this->getConnection()->insertMultiple($contentListTable, $toInsert);
        }
    }

    /**
     * Merge array values with an additionnal value
     *
     * @param array $array
     * @param mixed $value
     * @param $key1
     * @param $key2
     * @return array
     */
    protected function associatedValues($array, $value, $key1, $key2)
    {
        $return = [];

        foreach ($array as $val) {
            $return[] = [$key1 => $val, $key2 => $value];
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $contentList)
    {
        $where = [self::ID . ' = (?)' => (int)$contentList->getId()];

        $this->getConnection()->delete($this->getContentListStoreTable(), $where);

        parent::_beforeDelete($contentList);

        return $this;
    }
}
