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

namespace Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts;

/**
 * Class LayoutFieldLabel
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
 */
class LayoutFieldLabel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get layout field label option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            ['value' => '0', 'label' => __('Hide label')],
            ['value' => '1', 'label' => __('Show label over the element')],
            ['value' => '2', 'label' => __('Show label below the element')],
            ['value' => '3', 'label' => __('Show label inline')],
        ];

        return $return;
    }
}
