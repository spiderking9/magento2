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

namespace Blackbird\ContentManager\Model\ContentList\Layout;

use Blackbird\ContentManager\Model\ResourceModel\ContentList\Layout\Field as ResourceField;

/**
 * Class Field
 *
 * @package Blackbird\ContentManager\Model\ContentList\Layout
 */
class Field extends \Blackbird\ContentManager\Model\ContentType\Layout\Field
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceField::class);
        $this->setIdFieldName(self::ID);
        $this->setType('field');
    }
}
