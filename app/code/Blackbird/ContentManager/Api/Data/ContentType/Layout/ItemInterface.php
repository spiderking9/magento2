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

namespace Blackbird\ContentManager\Api\Data\ContentType\Layout;

/**
 * Interface ItemInterface
 *
 * @package Blackbird\ContentManager\Api\Data\ContentType\Layout
 * @api
 */
interface ItemInterface
{
    /** data keys value */
    const CT_ID = 'ct_id';
    const COLUMN = 'column';
    const SORT_ORDER = 'sort_order';
    const LABEL = 'label';
    const HTML_TAG = 'html_tag';
    const HTML_ID = 'html_id';
    const HTML_CLASS = 'html_class';
    const HTML_LABEL_TAG = 'html_label_tag';
}
