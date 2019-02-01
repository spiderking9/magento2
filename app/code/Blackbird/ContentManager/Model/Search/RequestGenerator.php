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

namespace Blackbird\ContentManager\Model\Search;

use Blackbird\ContentManager\Api\Data\AttributeMetadataInterface;
use Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\CollectionFactory;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * Class RequestGenerator
 *
 * @package Blackbird\ContentManager\Model\Search
 */
class RequestGenerator
{
    /** Filter name suffix */
    const FILTER_SUFFIX = '_filter';

    /** Bucket name suffix */
    const BUCKET_SUFFIX = '_bucket';

    /**
     * @var CollectionFactory
     */
    protected $_contentAttributeCollectionFactory;

    /**
     * RequestGenerator constructor
     *
     * @param CollectionFactory $contentAttributeCollectionFactory
     */
    public function __construct(CollectionFactory $contentAttributeCollectionFactory)
    {
        $this->_contentAttributeCollectionFactory = $contentAttributeCollectionFactory;
    }

    /**
     * Generate dynamic fields requests
     *
     * @return array
     */
    public function generate()
    {
        $requests = [
            'quick_search_container_contentmanager' => $this->generateRequest(AttributeMetadataInterface::IS_SEARCHABLE,
                'quick_search_container_contentmanager', true),
        ];

        return $requests;
    }

    /**
     * Generate search request
     *
     * @param string $attributeType
     * @param string $container
     * @param bool $useFulltext
     * @return array
     */
    protected function generateRequest($attributeType, $container, $useFulltext)
    {
        $request = [];
        foreach ($this->getSearchableAttributes() as $attribute) {
            if ($attribute->getData($attributeType)) {
                $queryName = $attribute->getAttributeCode() . '_query';

                $request['queries'][$container]['queryReference'][] = [
                    'clause' => 'should',
                    'ref' => $queryName,
                ];
                $filterName = $attribute->getAttributeCode() . self::FILTER_SUFFIX;
                $request['queries'][$queryName] = [
                    'name' => $queryName,
                    'type' => QueryInterface::TYPE_FILTER,
                    'filterReference' => [['ref' => $filterName]],
                ];
                $bucketName = $attribute->getAttributeCode() . self::BUCKET_SUFFIX;
                if ($attribute->getBackendType() == 'decimal') {
                    $request['filters'][$filterName] = [
                        'type' => FilterInterface::TYPE_RANGE,
                        'name' => $filterName,
                        'field' => $attribute->getAttributeCode(),
                        'from' => '$' . $attribute->getAttributeCode() . '.from$',
                        'to' => '$' . $attribute->getAttributeCode() . '.to$',
                    ];
                    $request['aggregations'][$bucketName] = [
                        'type' => BucketInterface::TYPE_DYNAMIC,
                        'name' => $bucketName,
                        'field' => $attribute->getAttributeCode(),
                        'method' => 'manual',
                        'metric' => [["type" => "count"]],
                    ];
                } else {
                    $request['filters'][$filterName] = [
                        'type' => FilterInterface::TYPE_TERM,
                        'name' => $filterName,
                        'field' => $attribute->getAttributeCode(),
                        'value' => '$' . $attribute->getAttributeCode() . '$',
                    ];
                    $request['aggregations'][$bucketName] = [
                        'type' => BucketInterface::TYPE_TERM,
                        'name' => $bucketName,
                        'field' => $attribute->getAttributeCode(),
                        'metric' => [["type" => "count"]],
                    ];
                }
            }
            if ($useFulltext) {
                $request['queries']['search']['match'][] = [
                    'field' => $attribute->getAttributeCode(),
                    'boost' => $attribute->getSearchWeight() ?: 1,
                ];
            }
        }

        return $request;
    }

    /**
     * Retrieve searchable attributes
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute[]
     */
    protected function getSearchableAttributes()
    {
        $contentAttributes = $this->_contentAttributeCollectionFactory->create();
        $contentAttributes->addFieldToFilter('is_searchable', 1);

        return $contentAttributes;
    }
}
