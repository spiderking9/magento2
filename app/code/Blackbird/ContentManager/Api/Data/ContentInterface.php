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

namespace Blackbird\ContentManager\Api\Data;

/**
 * Interface ContentInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 */
interface ContentInterface
{
    const TABLE = 'blackbird_contenttype_entity';

    const ID = 'entity_id';
    const ENTITY_TYPE_ID = 'entity_type_id';
    const CT_ID = 'ct_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TITLE = 'title';
    const STATUS = 'status';
    const URL_KEY = 'url_key';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'description';
    const META_KEYWORDS = 'keywords';
    const OG_TITLE = 'og_title';
    const OG_DESCRIPTION = 'og_description';
    const OG_URL = 'og_url';
    const OG_TYPE = 'og_type';
    const OG_IMAGE = 'og_image';
    const STORE_ID = 'store_id';
    const IMPORT_IDENTIFIER = 'import_identifier';

    /**
     * Default Values
     */
    const DEFAULT_URL = 'regenerate_url';
    const DEFAULT_TITLE = 'use_default_title';
    const DEFAULT_KEYWORDS = 'use_default_keywords';
    const DEFAULT_DESCR = 'use_default_description';
    const DEFAULT_META_TITLE = 'use_default_meta_title';
    const DEFAULT_OG_TITLE = 'use_default_og_title';
    const DEFAULT_OG_DESCR = 'use_default_og_description';
    const DEFAULT_OG_URL = 'use_default_og_url';
    const DEFAULT_OG_TYPE = 'use_default_og_type';
    const DEFAULT_OG_IMAGE = 'use_default_og_image';
}
