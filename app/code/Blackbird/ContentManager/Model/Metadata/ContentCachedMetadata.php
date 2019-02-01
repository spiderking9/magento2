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

use Blackbird\ContentManager\Api\ContentMetadataInterface;

/**
 * Cached content attribute metadata service
 *
 * Class ContentCachedMetadata
 *
 * @package Blackbird\ContentManager\Model\Metadata
 */
class ContentCachedMetadata extends CachedMetadata implements ContentMetadataInterface
{
    /**
     * ContentCachedMetadata constructor
     *
     * @param ContentMetadata $metadata
     */
    public function __construct(ContentMetadata $metadata)
    {
        parent::__construct($metadata);
    }
}
