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
 * Class Breadcrumb
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentList
 */
class Breadcrumb implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get breadcrumb option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            ['value' => '', 'label' => __('No Breadcrumb')],
            ['value' => 'title', 'label' => __('Page Title')],
            ['value' => 'breadcrumb_custom_title', 'label' => __('Custom Breadcrumb')],
        ];

        return $return;
    }
}
