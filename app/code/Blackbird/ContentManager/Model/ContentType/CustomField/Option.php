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

namespace Blackbird\ContentManager\Model\ContentType\CustomField;

use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\Option as ResourceOption;

/**
 * Custom Field Model
 *
 * Class Option
 *
 * @package Blackbird\ContentManager\Model\ContentType\CustomField
 */
class Option extends \Blackbird\ContentManager\Model\AbstractModel
    implements \Blackbird\ContentManager\Api\Data\ContentType\CustomField\OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if (!$this->hasData(self::TITLE) && $this->hasData("label")) {
            $this->setTitle($this->getLabel());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->_getData(self::SORT_ORDER);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return __($this->_getData(self::TITLE))->render();
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_getData(self::VALUE);
    }

    /**
     * Get default value
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_getData(self::DEFAULT_VAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceOption::class);
        $this->setIdFieldName(self::ID);
    }
}
