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

namespace Blackbird\ContentManager\Model;

/**
 * Class AbstractModel
 *
 * @package Blackbird\ContentManager\Model
 */
abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Identifier of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = '0';

    /**
     * Flag which allow detect object state: is it new copied object
     *
     * @var bool
     */
    protected $_isObjectCopied = false;

    /**
     * Is object copied
     *
     * @param null $flag
     * @return bool|null
     */
    public function isObjectCopied($flag = null)
    {
        if ($flag !== null) {
            $this->_isObjectCopied = $flag;
        }

        return $this->_isObjectCopied;
    }
}
