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

namespace Blackbird\ContentManager\Model\Config\Source\Content\Widget\ContentList;

/**
 * Class AttributeShow
 *
 * @package Blackbird\ContentManager\Model\Config\Source\Content\Widget\ContentList
 */
class AttributeShow implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'attribute', 'label' => __('Attribute'), 'input' => 'select', 'source' => ''],
            ['value' => 'label', 'label' => __('Label'), 'input' => 'text'],
            ['value' => 'label_type', 'label' => __('Label Type'), 'input' => 'select', 'source' => ''],
            ['value' => 'html_label_tag', 'label' => __('Html Label Tag'), 'input' => 'text'],
            ['value' => 'html_tag', 'label' => __('Html Tag'), 'input' => 'text'],
            ['value' => 'html_id', 'label' => __('Html Id'), 'input' => 'text'],
            ['value' => 'html_classes', 'label' => __('Html Classes'), 'input' => 'text'],
            [
                'value' => 'has_link',
                'label' => __('Has Link'),
                'input' => 'select',
                'source' => 'Magento\Config\Model\Config\Source\Yesno',
            ],
            ['value' => 'output_format', 'label' => __('Output Format'), 'input' => 'select', 'source' => ''],
            ['value' => 'width', 'label' => __('Width'), 'input' => 'text'],
            ['value' => 'height', 'label' => __('Height'), 'input' => 'text'],
            [
                'value' => 'image_link',
                'label' => __('Image Link'),
                'input' => 'select',
                'source' => 'Magento\Config\Model\Config\Source\Yesno',
            ],
        ];
    }
}
