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
 * Interface CustomFieldsetInterface
 *
 * @package Blackbird\ContentManager\Api\Data\ContentType
 * @api
 */
interface CustomFieldsetInterface
{
    /** data keys value */
    const ID = 'fieldset_id';
    const CT_ID = 'ct_id';
    const TITLE = 'title';
    const SORT_ORDER = 'sort_order';
}
