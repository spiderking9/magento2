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

/**
 * Class Attribute
 *
 * @package Blackbird\ContentManager\Model\ResourceModel
 */
class Attribute
    extends \Magento\Eav\Model\ResourceModel\Attribute // \Magento\Eav\Model\ResourceModel\Entity\Attribute ??
{
    /**
     * @var string
     */
    protected $_eavWebsiteTable = '';

    /**
     * @var string
     */
    protected $_formAttributeTable = '';

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        if (empty($this->_eavWebsiteTable)) {
            $this->_eavWebsiteTable = $this->getTable('blackbird_contenttype_eav_attribute_website');
        }

        return $this->_eavWebsiteTable;
    }

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids is stored
     *
     * @return string|null
     */
    protected function _getFormAttributeTable()
    {
        if (empty($this->_formAttributeTable)) {
            $this->_formAttributeTable = $this->getTable('blackbird_contenttype_form_attribute');
        }

        return $this->_formAttributeTable;
    }
}
