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

namespace Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout;

use Blackbird\ContentManager\Api\Data\ContentType\Layout\BlockInterface;

/**
 * Layout Block Resource Model
 */
class Block extends \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blackbird_contenttype_list_layout_block', BlockInterface::ID);
    }
}
