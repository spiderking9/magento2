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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type;

/**
 * Class AbstractType
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type
 */
abstract class AbstractType extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_name = 'abstract';

    /**
     * Get widget fieldset name
     *
     * @return string
     */
    public function getFieldsetName()
    {
        return 'contenttype[fieldsets]';
    }

    /**
     * Get widget fieldset id
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return 'contenttype_fieldset';
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return '[fields]';
    }

    /**
     * Get field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'field';
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
        return '[types]';
    }

    /**
     * Get type id
     *
     * @return string
     */
    public function getTypeId()
    {
        return 'type';
    }
}
