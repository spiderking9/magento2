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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Group;

use Blackbird\ContentManager\Model\ContentList\Layout\Group;
use Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Group as ResourceGroup;

/**
 * Layout Group Resource Model Collection
 */
class Collection extends \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group\Collection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Group::class, ResourceGroup::class);
    }
}
