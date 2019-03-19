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

namespace Blackbird\ContentManager\Model\Metadata;

use Blackbird\ContentManager\Api\MetadataInterface;

/**
 * Cached attribute metadata service
 *
 * Class CachedMetadata
 *
 * @package Blackbird\ContentManager\Model\Metadata
 */
class CachedMetadata implements MetadataInterface
{
    const CACHE_SEPARATOR = ';';

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var array
     */
    protected $attributeMetadataCache = [];

    /**
     * @var array
     */
    protected $attributesCache = [];

    /**
     * @var \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface[]
     */
    protected $allAttributeMetadataCache = null;

    /**
     * @var \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface[]
     */
    protected $customAttributesMetadataCache = null;

    /**
     * CachedMetadata constructor
     *
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $key = $formCode;
        if (isset($this->attributesCache[$key])) {
            return $this->attributesCache[$key];
        }

        $value = $this->metadata->getAttributes($formCode);
        $this->attributesCache[$key] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($attributeCode)
    {
        $key = $attributeCode;
        if (isset($this->attributeMetadataCache[$key])) {
            return $this->attributeMetadataCache[$key];
        }

        $value = $this->metadata->getAttributeMetadata($attributeCode);
        $this->attributeMetadataCache[$key] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributesMetadata()
    {
        if ($this->allAttributeMetadataCache !== null) {
            return $this->allAttributeMetadataCache;
        }

        $this->allAttributeMetadataCache = $this->metadata->getAllAttributesMetadata();

        return $this->allAttributeMetadataCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        if ($this->customAttributesMetadataCache !== null) {
            return $this->customAttributesMetadataCache;
        }

        $this->customAttributesMetadataCache = $this->metadata->getCustomAttributesMetadata();

        return $this->customAttributesMetadataCache;
    }
}
