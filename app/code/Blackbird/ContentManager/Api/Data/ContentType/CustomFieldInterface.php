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

namespace Blackbird\ContentManager\Api\Data\ContentType;

/**
 * Interface CustomFieldInterface
 *
 * @package Blackbird\ContentManager\Api\Data\ContentType
 * @api
 */
interface CustomFieldInterface
{
    /** data keys value */
    const ID           = 'option_id';
    const CT_ID        = 'ct_id';
    const SORT_ORDER   = 'sort_order';
    const TYPE         = 'type';
    const FIELDSET_ID  = 'fieldset_id';
    const IDENTIFIER   = 'identifier';
    const NOTE         = 'note';
    const STORE_ID     = 'store_id';
    const TITLE        = 'title';
    const ATTRIBUTE    = 'attribute';
    const ATTRIBUTE_ID = 'attribute_id';

    const PREV_GROUP = 'previous_group';
    const PREV_TYPE  = 'previous_type';

    /** GROUP */
    const GROUP_TEXT     = 'text';
    const GROUP_FILE     = 'file';
    const GROUP_SELECT   = 'select';
    const GROUP_DATE     = 'date';
    const GROUP_RELATION = 'relation';

    /** TYPE */
    const TYPE_FIELD        = 'field';
    const TYPE_AREA         = 'area';
    const TYPE_PASSWORD     = 'password';
    const TYPE_FILE         = 'file';
    const TYPE_IMAGE        = 'image';
    const TYPE_DROP_DOWN    = 'drop_down';
    const TYPE_RADIO        = 'radio';
    const TYPE_CHECKBOX     = 'checkbox';
    const TYPE_MULTIPLE     = 'multiple';
    const TYPE_DATE         = 'date';
    const TYPE_DATE_TIME    = 'date_time';
    const TYPE_PRODUCT      = 'product';
    const TYPE_CUSTOMER     = 'customer';
    const TYPE_CATEGORY     = 'category';
    const TYPE_CONTENT      = 'content';
    const TYPE_CONTENT_LIST = 'content-list';
    const TYPE_ATTRIBUTE    = 'attribute';
    const TYPE_COUNTRY      = 'country';
    const TYPE_CURRENCY     = 'currency';
    const TYPE_LOCALE       = 'locale';
    const TYPE_TIME         = 'time';

    /** SPECIFIC ATTRIBUTES FOR CONTENT */
    const CT_IDENTIFIER = 'content_type';

    /** SPECIFIC ATTRIBUTES FOR IMAGE */
    const IMG_TITLE      = 'img_title';
    const IMG_ALT        = 'img_alt';
    const IMG_URL        = 'img_url';
    const CROP           = 'crop';
    const CROP_W         = 'crop_w';
    const CROP_H         = 'crop_h';
    const KEEP_RATIO     = 'keep_aspect_ratio';
    const FILE_PATH      = 'file_path';
    const FILE_EXTENSION = 'file_extension';

    /** Form attribute */
    const OPTIONS = 'select';
}
