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

namespace Blackbird\ContentManager\Api;

/**
 * Interface ContentMetadataInterface
 * Interface for retrieval information about content attributes metadata.
 *
 * @package Blackbird\ContentManager\Api
 * @api
 * @todo NEED CHECKUP
 */
interface ContentMetadataInterface extends MetadataInterface
{
    const ATTRIBUTE_SET_ID_CONTENT = 1;

    const ENTITY_TYPE_CONTENT = 'contenttype_content';

    const DATA_INTERFACE_NAME = \Blackbird\ContentManager\Api\Data\ContentInterface::class;
}
