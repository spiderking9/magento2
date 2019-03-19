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

namespace Blackbird\ContentManager\Model\Rule\Condition\Sql;

use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class Builder
 *
 * @package Blackbird\ContentManager\Model\Rule\Condition\Sql
 */
class Builder extends \Magento\Rule\Model\Condition\Sql\Builder
{
    /**
     * @var array
     */
    protected $_conditionOperatorMap = [
        '==' => ':field = ?',
        '!=' => ':field <> ?',
        '>=' => ':field >= ?',
        '>' => ':field > ?',
        '<=' => ':field <= ?',
        '<' => ':field < ?',
        '{}' => 'FIND_IN_SET(?, :field)',
        '!{}' => 'NOT FIND_IN_SET(?, :field)',
        '()' => 'FIND_IN_SET(?, :field)',
        '!()' => 'NOT FIND_IN_SET(?, :field)',
    ];

    /**
     * {@inheritdoc}
     */
    protected function _getMappedSqlCondition(AbstractCondition $condition, $value = '', $isDefaultStoreUsed = true)
    {
        $argument = $condition->getMappedSqlField();

        // If rule hasn't valid argument - create negative expression to prevent incorrect rule behavior.
        if (empty($argument)) {
            return $this->_expressionFactory->create(['expression' => '1 = -1']);
        }

        $conditionOperator = $condition->getOperatorForValidate();

        if (!isset($this->_conditionOperatorMap[$conditionOperator])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Unknown condition operator'));
        }

        $defaultValue = 0;
        // Check if attribute has a table with default value and add it to the query
        if ($condition->hasDefaultValue()) {
            $defaultField = 'at_' . $condition->getAttribute() . '_default.value';
            $defaultValue = $this->_connection->quoteIdentifier($defaultField);
        }

        $sql = str_replace(
            ':field',
            $this->_connection->getIfNullSql($this->_connection->quoteIdentifier($argument), $defaultValue),
            $this->_conditionOperatorMap[$conditionOperator]
        );

        return $this->_expressionFactory->create(
            ['expression' => $value . $this->_connection->quoteInto($sql, $condition->getBindArgumentValue())]
        );
    }
}
