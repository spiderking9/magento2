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

namespace Blackbird\ContentManager\Model\ResourceModel\Content\Attribute;

/**
 * Content Type Resource Model Collection
 *
 * Class Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\Content\Attribute
 */
class Collection extends \Magento\Eav\Model\ResourceModel\Attribute\Collection
{
    /**
     * @var string
     */
    protected $_eavWebsiteTable = '';

    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'contenttype_content';

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return $this->_entityTypeCode;
    }

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
}
