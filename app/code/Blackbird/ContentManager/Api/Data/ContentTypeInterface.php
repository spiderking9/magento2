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
 * Interface ContentTypeInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 */
interface ContentTypeInterface
{
    /** data keys value */
    const ID = 'ct_id';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const CREATED_TIME = 'created_time';
    const UPDATE_TIME = 'update_time';
    const DEFAULT_STATUS = 'default_status';
    const VISIBILITY = 'visibility';
    const DESCRIPTION = 'description';
    const DEFAULT_URL = 'default_url';
    const BREADCRUMB = 'breadcrumb';
    const BREADCRUMB_PREV_LINK = 'breadcrumb_prev_link';
    const BREADCRUMB_PREV_NAME = 'breadcrumb_prev_name';
    const PAGE_TITLE = 'page_title';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const META_ROBOTS = 'meta_robots';
    const OG_TITLE = 'og_title';
    const OG_URL = 'og_url';
    const OG_DESCRIPTION = 'og_description';
    const OG_IMAGE = 'og_image';
    const OG_TYPE = 'og_type';
    const SITEMAP_ENABLE = 'sitemap_enable';
    const SITEMAP_FREQUENCY = 'sitemap_frequency';
    const SITEMAP_PRIORITY = 'sitemap_priority';
    const SEARCH_ENABLED = 'search_enabled';
    const LAYOUT = 'layout';
    const ROOT_TEMPLATE = 'root_template';
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
    const CT_FILE_FOLDER = 'contentmanager/content/';
    const CT_IMAGE_CROPPED_FOLDER = 'crop/';
    const DEFAULT_IMPORT_IDENTIFIER_NAME = 'default_import_identifier_name';
    const DEFAULT_IMPORT_IDENTIFIER_VALUE = 'default_import_identifier_value';

    /**
     * Set ContentType Visbility
     *
     * @param int $visibility
     * @return $this
     */
    public function setVisibility($visibility);

    /**
     * Get ContentType Visibility
     *
     * @return int
     */
    public function getVisibility();
}
