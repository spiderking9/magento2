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

namespace Blackbird\ContentManager\Model\Rule\Condition;

use Blackbird\ContentManager\Model\ResourceModel\Content\Collection as ContentCollection;
use Magento\Rule\Model\Condition\Combine as CombineAbstract;

/**
 * ContentManager Rule Combine Condition data model
 *
 * Class Combine
 *
 * @package Blackbird\ContentManager\Model\Rule\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Blackbird\ContentManager\Model\Rule\Condition\ContentFactory
     */
    protected $_contentFactory;

    /**
     * {@inheritdoc}
     */
    protected $elementName = 'parameters';

    /**
     * @var array
     */
    protected $_aliasAttributes = [];

    /**
     * Combine constructor
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param ContentFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Blackbird\ContentManager\Model\Rule\Condition\ContentFactory $conditionFactory,
        array $data = []
    ) {
        $this->_contentFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType(\Blackbird\ContentManager\Model\Rule\Condition\Combine::class);
    }

    /**
     * Get new child select option
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $contentAttributes = $this->_contentFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($contentAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Blackbird\ContentManager\Model\Rule\Condition\Content|' . $code,
                'label' => $label,
            ];
        }
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), [
            [
                'value' => \Blackbird\ContentManager\Model\Rule\Condition\Combine::class,
                'label' => __('Conditions Combination'),
            ],
            ['label' => __('Content Attribute'), 'value' => $attributes],
        ]);

        return $conditions;
    }

    /**
     * Collect validated attributes for Content Collection
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\Collection $contentCollection
     * @param \Magento\Rule\Model\Condition\Combine $combine
     * @return $this
     */
    public function collectValidatedAttributes(ContentCollection $contentCollection, CombineAbstract $combine = null)
    {
        // Init: reset attributes alias
        $this->_aliasAttributes = [];

        if ($combine !== null) {
            $conditions = $combine->getConditions();
        } else {
            $conditions = $this->getConditions();
        }

        foreach ($conditions as $condition) {
            if ($condition instanceof CombineAbstract) {
                $this->collectValidatedAttributes($contentCollection, $condition);
            } else {
                if (!isset($this->_aliasAttributes[$condition->getAttribute()])) {
                    $condition->addToCollection($contentCollection);
                    $this->_aliasAttributes[$condition->getAttribute()] = true;
                }
            }
        }

        return $this;
    }
}
