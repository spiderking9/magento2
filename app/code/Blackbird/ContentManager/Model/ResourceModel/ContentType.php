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

namespace Blackbird\ContentManager\Model\ResourceModel;

use Blackbird\ContentManager\Api\Data\ContentTypeInterface;

/**
 * Content Type Resource Model
 *
 * Class ContentType
 *
 * @package Blackbird\ContentManager\Model\ResourceModel
 */
class ContentType extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('blackbird_contenttype', ContentTypeInterface::ID);
    }
}
