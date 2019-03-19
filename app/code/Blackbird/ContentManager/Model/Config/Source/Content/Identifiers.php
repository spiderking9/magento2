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

namespace Blackbird\ContentManager\Model\Config\Source\Content;

/**
 * Class Identifiers
 *
 * @package Blackbird\ContentManager\Model\Config\Source\Content
 */
class Identifiers implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get option list for identifiers
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (static::toArray() as $identifier) {
            $result[] = [
                'label' => $identifier,
                'value' => $identifier,
            ];
        }

        return $result;
    }

    /**
     * Get option array
     *
     * @todo refact, remove static
     * @return array
     */
    public static function toArray()
    {
        return [
            'entity_id',
            'entity_type_id',
            'ct_id',
            'created_at',
            'updated_at',
            'content',
            'title',
            'meta_title',
            'description',
            'keywords',
            'robots',
            'og_title',
            'og_url',
            'og_description',
            'og_image',
            'og_type',
            'use_default_title',
            'use_default_description',
            'use_default_keywords',
            'use_default_robots',
            'use_default_og_title',
            'use_default_og_url',
            'use_default_og_description',
            'use_default_og_image',
            'use_default_og_type',
            'status',
            'store',
            'ct_identifier',
            'store_code',
            'identifier',
        ];
    }
}
