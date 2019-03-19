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

namespace Blackbird\ContentManager\Model\ContentType\Layout;

/**
 * Class AbstractModel
 *
 * @package Blackbird\ContentManager\Model\ContentType\Layout
 */
abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentType\Layout\ItemInterface
{
    /**
     * @var string
     */
    protected $_type;

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }
}
