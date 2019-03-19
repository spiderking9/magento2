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
 * Interface FlagInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 */
interface FlagInterface
{
    /** data keys value */
    const ID = 'store_id';
    const VALUE = 'value';
    const FLAG_PATH = 'Blackbird_ContentManager::images/flags/';
    const DEFAULT_FLAG = 'world.png';
}
