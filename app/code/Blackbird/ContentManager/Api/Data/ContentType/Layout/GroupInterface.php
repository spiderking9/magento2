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
 * Interface GroupInterface
 *
 * @package Blackbird\ContentManager\Api\Data\ContentType\Layout
 * @api
 */
interface GroupInterface extends ItemInterface
{
    /** data keys value */
    const ID = 'layout_group_id';
    const PARENT_ID = 'parent_layout_group_id';
    const HTML_NAME = 'html_name';

    /**
     * Return all children
     *
     * @return array
     */
    public function getChildren();
}
