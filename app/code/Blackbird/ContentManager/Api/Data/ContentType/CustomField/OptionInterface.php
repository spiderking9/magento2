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

namespace Blackbird\ContentManager\Api\Data\ContentType\CustomField;

/**
 * Interface OptionInterface
 *
 * @package Blackbird\ContentManager\Api\Data\ContentType\CustomField
 * @api
 */
interface OptionInterface
{
    /** data keys value */
    const ID = 'option_type_id';
    const FIELD_ID = 'option_id';
    const TITLE = 'title';
    const STORE_ID = 'store_id';
    const SORT_ORDER = 'sort_order';
    const VALUE = 'value';
    const DEFAULT_VAL = 'default';
}
