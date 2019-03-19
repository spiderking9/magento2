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

namespace Blackbird\ContentManager\Setup;

use Magento\Eav\Setup\EavSetup;

/**
 * Description of ContentSetup
 *
 * Class ContentSetup
 *
 * @package Blackbird\ContentManager\Setup
 */
class ContentSetup extends EavSetup
{
    /**
     * Gets default entities and attributes
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        // Const
        $global = \Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute::SCOPE_STORE;

        return [
            \Blackbird\ContentManager\Model\Content::ENTITY => [
                'entity_model' => \Blackbird\ContentManager\Model\ResourceModel\Content::class,
                'attribute_model' => \Blackbird\ContentManager\Model\ResourceModel\Eav\Attribute::class,
                'table' => 'blackbird_contenttype_entity',
                'additional_attribute_table' => 'blackbird_contenttype_eav_attribute',
                'entity_attribute_collection' => \Blackbird\ContentManager\Model\ResourceModel\Content\Attribute\Collection::class,
                'attributes' => [
                    /**
                     * General informations
                     */
                    'title' => [
                        'type' => 'varchar',
                        'label' => 'Title',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => true,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_title' => [
                        'type' => 'int',
                        'label' => 'Use Default Title',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'status' => [
                        'type' => 'int',
                        'label' => 'Status',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    /**
                     * Url management
                     */
                    'url_key' => [
                        'type' => 'varchar',
                        'label' => 'URL Key',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                        'unique' => true,
                        'backend' => \Blackbird\ContentManager\Model\Entity\Attribute\Backend\UrlKey::class,
                    ],
                    /**
                     * Default Meta Tags
                     */
                    'meta_title' => [
                        'type' => 'varchar',
                        'label' => 'Meta Title',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'use_default_meta_title' => [
                        'type' => 'int',
                        'label' => 'Use Default Meta Title',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Meta Description',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_description' => [
                        'type' => 'int',
                        'label' => 'Use Default Meta Description',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'keywords' => [
                        'type' => 'text',
                        'label' => 'Meta Keywords',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_keywords' => [
                        'type' => 'int',
                        'label' => 'Use Default Meta Keywords',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'robots' => [
                        'type' => 'varchar',
                        'label' => 'Meta Robots',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    /**
                     * Default Open Graph
                     */
                    'og_title' => [
                        'type' => 'varchar',
                        'label' => 'OG Title',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_og_title' => [
                        'type' => 'int',
                        'label' => 'Use Default Meta Robots',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'og_description' => [
                        'type' => 'text',
                        'label' => 'OG Description',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_og_description' => [
                        'type' => 'int',
                        'label' => 'Use Default OG Description',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'og_url' => [
                        'type' => 'text',
                        'label' => 'OG Url',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_og_url' => [
                        'type' => 'int',
                        'label' => 'Use Default OG Url',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'og_type' => [
                        'type' => 'text',
                        'label' => 'OG Type',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_og_type' => [
                        'type' => 'int',
                        'label' => 'Use Default OG Type',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                    'og_image' => [
                        'type' => 'text',
                        'label' => 'OG Image',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'global' => $global,
                    ],
                    'use_default_og_image' => [
                        'type' => 'int',
                        'label' => 'Use Default OG Image',
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'searchable' => false,
                        'filterable' => false,
                        'global' => $global,
                        'comparable' => false,
                    ],
                ],
            ],
        ];
    }
}
