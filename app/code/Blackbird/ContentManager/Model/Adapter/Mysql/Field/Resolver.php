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

namespace Blackbird\ContentManager\Model\Adapter\Mysql\Field;

use Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\Collection as AttributeCollection;
use Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Search\Adapter\Mysql\Field\FieldFactory;
use Magento\Framework\Search\Adapter\Mysql\Field\FieldInterface;
use Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface;

/**
 * Class Resolver
 *
 * @package Blackbird\ContentManager\Model\Adapter\Mysql\Field
 */
class Resolver implements ResolverInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var AttributeCollection
     */
    private $attributeCollection;

    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * Resolver constructor
     *
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param FieldFactory $fieldFactory
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        FieldFactory $fieldFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $fields)
    {
        $resolvedFields = [];

        foreach ($fields as $field) {
            if ('*' === $field) {
                $resolvedFields = [$this->createField()];
                break;
            }

            $attribute = $this->getAttributeCollection()->getItemByColumnValue('attribute_code', $field);
            $attributeId = $attribute ? $attribute->getId() : 0;

            $resolvedFields[$field] = $this->createField($attributeId);
        }

        return $resolvedFields;
    }

    /**
     * Create a field for the indexer table
     *
     * @param int|null $attributeId
     * @return FieldInterface
     */
    private function createField($attributeId = null)
    {
        return $this->fieldFactory->create([
            'attributeId' => $attributeId,
            'column' => 'data_index',
            'type' => FieldInterface::TYPE_FULLTEXT,
        ]);
    }

    /**
     * Retrieve the current attribute collection instance
     *
     * @return AttributeCollection
     */
    private function getAttributeCollection()
    {
        if ($this->attributeCollection === null) {
            $this->attributeCollection = $this->attributeCollectionFactory->create();
        }

        return $this->attributeCollection;
    }
}
