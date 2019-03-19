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
 * Interface ContentLayoutInterface
 * Interface to manage layout and items
 *
 * @package Blackbird\ContentManager\Api
 * @api
 */
interface ContentLayoutInterface
{
    /**
     * Return list of layout items values
     *
     * @return array
     */
    public function getLayoutItemCollection();

    /**
     * Retrieve collection of layout group item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\Collection
     */
    public function getLayoutGroupItemCollection();

    /**
     * Retrieve collection of layout field item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\Collection
     */
    public function getLayoutFieldItemCollection();

    /**
     * Retrieve collection of layout block item
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\Collection
     */
    public function getLayoutBlockItemCollection();
}
