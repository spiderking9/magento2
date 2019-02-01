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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentType;

use Blackbird\ContentManager\Api\Data\ContentType\CustomFieldInterface;

/**
 * Custom Field Resource Model
 *
 * Class CustomField
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\ContentType
 */
class CustomField extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_optionTitleTable = null;

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blackbird_contenttype_option', CustomFieldInterface::ID);
    }

    /**
     * Perform actions after object save
     *
     * @param \Blackbird\ContentManager\Model\ContentType\CustomField|\Magento\Framework\Model\AbstractModel $customField
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $customField)
    {
        parent::_afterSave($customField);

        // Save title
        $this->_saveTitle($customField);

        return $this;
    }

    /**
     * Save the title
     *
     * @param \Blackbird\ContentManager\Model\ContentType\CustomField|\Magento\Framework\Model\AbstractModel $customField
     */
    protected function _saveTitle(\Magento\Framework\Model\AbstractModel $customField)
    {
        $table = $this->getOptionTitleTable();

        // Check if it already exists
        $select = $this->getConnection()
            ->select()
            ->from($table)
            ->where('option_id = ?', $customField->getId())
            ->where('store_id = ?', \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID);
        $exist = $this->getConnection()->fetchOne($select);

        $bindValues = [
            $customField::ID => $customField->getId(),
            $customField::STORE_ID => \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID,
            $customField::TITLE => $customField->getTitle(),
        ];

        if ($exist === false) {
            // Insert custom field title
            $this->getConnection()->insert($table, $bindValues);
        } else {
            $whereClause = [
                'option_id = (?)' => $customField->getId(),
                'store_id = (?)' => \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID,
            ];
            // Update custom field title
            $this->getConnection()->update($table, $bindValues, $whereClause);
        }
    }

    /**
     * Retrieve the 'blackbird_contenttype_option_title' table
     *
     * @return string
     */
    public function getOptionTitleTable()
    {
        if (empty($this->_optionTitleTable)) {
            $this->_optionTitleTable = $this->getTable('blackbird_contenttype_option_title');
        }

        return $this->_optionTitleTable;
    }

    /**
     * Perform actions before object delete
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $customField
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $customField)
    {
        parent::_beforeDelete($customField);

        // Delete title
        $this->_deleteTitle($customField);

        return $this;
    }

    /**
     * Delete the title
     *
     * @param \Magento\Framework\Model\AbstractModel $customField
     */
    protected function _deleteTitle(\Magento\Framework\Model\AbstractModel $customField)
    {
        $whereClause = [
            'option_id = (?)' => $customField->getId(),
        ];

        $this->getConnection()->delete($this->getOptionTitleTable(), $whereClause);
    }
}
