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

namespace Blackbird\ContentManager\Model\Config\Source;

/**
 * Attribute weight options
 *
 * Class Weight
 *
 * @package Blackbird\ContentManager\Model\Config\Source
 */
class Weight implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Quick search weights
     *
     * @var int[]
     */
    protected $_weights = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    /**
     * Retrieve search weights as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        foreach ($this->getValues() as $value) {
            $res[] = ['value' => $value, 'label' => $value];
        }

        return $res;
    }

    /**
     * Retrieve search weights array
     *
     * @return int[]
     */
    public function getValues()
    {
        return $this->_weights;
    }
}
