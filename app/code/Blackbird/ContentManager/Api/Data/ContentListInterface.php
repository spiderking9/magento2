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
 * Interface ContentListInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 */
interface ContentListInterface
{
    /** data keys value */
    const ID = 'cl_id';
    const CT_ID = 'ct_id';
    const TITLE = 'title';
    const URL_KEY = 'url_key';
    const STATUS = 'status';
    const STORE_ID = 'store_id';
    const TEXT_BEFORE = 'text_before';
    const TEXT_AFTER = 'text_after';
    const PAGER = 'pager';
    const PAGER_POSITION = 'pager_position';
    const LIMIT_PAGE = 'limit_per_page';
    const LIMIT = 'limit_display';
    const ORDER_FIELD = 'order_field';
    const SORT_ORDER = 'sort_order';
    const CONDITIONS = 'conditions';
    const BREADCRUMB = 'breadcrumb';
    const BREADCRUMB_CUSTOM_TITLE = 'breadcrumb_custom_title';
    const BREADCRUMB_PREV_LINK = 'breadcrumb_prev_link';
    const BREADCRUMB_PREV_NAME = 'breadcrumb_prev_name';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const META_ROBOTS = 'meta_robots';
    const OG_TITLE = 'og_title';
    const OG_URL = 'og_url';
    const OG_DESCRIPTION = 'og_description';
    const OG_IMAGE = 'og_image';
    const OG_TYPE = 'og_type';
    const LAYOUT = 'layout';
    const ROOT_TEMPLATE = 'root_template';
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
}
