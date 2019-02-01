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

namespace Blackbird\ContentManager\Model\ResourceModel\Eav;

/**
 * Class Attribute
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\Eav
 */
class Attribute extends \Magento\Eav\Model\Entity\Attribute
    implements \Blackbird\ContentManager\Api\Data\ContentTypeAttributeInterface,
    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface
{
    const MODULE_NAME = 'Blackbird_ContentManager';

    const KEY_IS_GLOBAL = 'is_global';

    /**
     * Has attribute set infos
     *
     * @todo: manage attribute set: one for each content type
     * @return boolean
     */
    public function hasAttributeSetInfo()
    {
        return false;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData(self::KEY_IS_GLOBAL);
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Detect backend storage type using frontend input type
     *
     * @param string $type frontend_input field value
     * @return string backend_type field value
     */
    public function getBackendTypeByInput($type)
    {
        $fieldType = [
            'field' => 'varchar',
            'area' => 'text',
            'password' => 'varchar',
            'file' => 'varchar',
            'image' => 'varchar',
            'image_original' => 'varchar',
            'img_titl' => 'varchar',
            'img_alt' => 'varchar',
            'img_url' => 'varchar',
            'drop_down' => 'varchar',
            'radio' => 'varchar',
            'checkbox' => 'text',
            'multiple' => 'text',
            'date' => 'datetime',
            'date_time' => 'datetime',
            'time' => 'time',
            'product' => 'text',
            'category' => 'text',
            'content' => 'text',
            'integer' => 'int',
            'decimal' => 'decimal',
            'repeater_fields' => 'text',
        ];

        $field = parent::getBackendTypeByInput($type);

        if (empty($field)) {
            $field = isset($fieldType[$type]) ? $fieldType[$type] : 'text';
        }

        return $field;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Catalog\Model\ResourceModel\Attribute::class);
    }
}
