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

namespace Blackbird\ContentManager\Model\Config\Source\ContentType;

/**
 * Class Visibility
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType
 */
class Visibility implements \Magento\Framework\Data\OptionSourceInterface
{
    const VISIBLE = 1;
    const NOT_VISIBLE = 2;
    const REPEATER_FIELD = 3;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VISIBLE, 'label' => __('Visible [New Page] (Render the data from an URL)')],
            [
                'value' => self::NOT_VISIBLE,
                'label' => __('Not Visible [No Page] (Only data storage, but can be used in widgets)'),
            ],
        ];
    }
}
