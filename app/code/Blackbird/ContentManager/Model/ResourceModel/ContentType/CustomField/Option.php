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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField;

use Blackbird\ContentManager\Api\Data\ContentType\CustomField\OptionInterface;

/**
 * Custom Field Resource Model
 *
 * Class Option
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField
 */
class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_optionTypeTitleTable = '';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('blackbird_contenttype_option_type_value', OptionInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $option)
    {
        parent::_afterSave($option);

        // Save title
        $this->_saveTitle($option);

        return $this;
    }

    /**
     * Save title method
     *
     * @param \Blackbird\ContentManager\Model\ContentType\CustomField\Option $option
     */
    protected function _saveTitle(\Blackbird\ContentManager\Model\ContentType\CustomField\Option $option)
    {
        $table = $this->getOptionTypeTitleTable();
        $whereClause = [
            'option_type_id = (?)' => $option->getId(),
            'store_id = (?)' => \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID,
        ];
        $bindValues = [
            $option::ID => $option->getId(),
            $option::STORE_ID => \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID,
            $option::TITLE => $option->getTitle(),
        ];

        $select = $this->getConnection()
            ->select()
            ->from($table)
            ->where('option_type_id = ?', $option->getId())
            ->where('store_id = ?', \Blackbird\ContentManager\Model\AbstractModel::DEFAULT_STORE_ID);
        $exist = $this->getConnection()->fetchOne($select);

        if ($exist === false) {
            // Insert option title
            $this->getConnection()->insert($table, $bindValues);
        } else {
            // Update option title
            $this->getConnection()->update($table, $bindValues, $whereClause);
        }
    }

    /**
     * Retrieve the 'blackbird_contenttype_option_type_title' table
     *
     * @return string
     */
    public function getOptionTypeTitleTable()
    {
        if (empty($this->_optionTypeTitleTable)) {
            $this->_optionTypeTitleTable = $this->getTable('blackbird_contenttype_option_type_title');
        }

        return $this->_optionTypeTitleTable;
    }

    /**
     * Call the delete title method before delete the current option
     *
     * @param \Magento\Framework\Model\AbstractModel $option
     * @return $this|void
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $option)
    {
        parent::_beforeDelete($option);

        // Delete title
        $this->_deleteTitle($option);
    }

    /**
     * Delete the title
     *
     * @param \Magento\Framework\Model\AbstractModel $option
     */
    protected function _deleteTitle(\Magento\Framework\Model\AbstractModel $option)
    {
        $whereClause = [
            'option_type_id = (?)' => $option->getId(),
        ];

        $this->getConnection()->delete($this->getOptionTypeTitleTable(), $whereClause);
    }
}
