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

namespace Blackbird\ContentManager\Model\Config\Source\ContentList;

/**
 * Class PagerPosition
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentList
 */
class PagerPosition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get pager position option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            ['value' => '0', 'label' => __('Top')],
            ['value' => '1', 'label' => __('Bottom')],
            ['value' => '2', 'label' => __('Top & Bottom')],
        ];

        return $return;
    }
}
