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

namespace Blackbird\ContentManager\Model\ResourceModel\Content;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;
use Magento\Store\Model\Store;

/**
 * Content Resource Model Collection
 *
 * @package Blackbird\ContentManager\Model\ResourceModel\Content
 */
class Collection extends \Blackbird\ContentManager\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Add store availability filter. Include availability content for store website
     *
     * @param null|string|bool|int|Store $store
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        $this->setStoreId(($store === null) ? $this->getStoreId() : $this->_storeManager->getStore($store)->getId());

        return $this;
    }

    /**
     * Filter the collection with visible content
     *
     * @param bool $enabled
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addIsVisibleFilter($enabled = true)
    {
        if ($enabled) {
            $this->addAttributeToFilter(Content::STATUS, 1);
        }

        return $this->addVisibilityFilter(Visibility::VISIBLE);
    }

    /**
     * Add visibility filter
     *
     * @param $visibility
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addVisibilityFilter($visibility)
    {
        $this->joinTable(['bct' => $this->getTable('blackbird_contenttype')], 'ct_id=ct_id',
            ['visibility' => 'visibility'], ['visibility' => $visibility]);

        return $this;
    }

    /**
     * Add a content type identifier to the filter
     *
     * @param string|int|array|ContentType $contentType identifier, id or ContentType object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addContentTypeFilter($contentType)
    {
        $contentTypes = is_array($contentType) ? $contentType : [$contentType];
        $isIdentifier = false;
        $ctFilters = [];

        foreach ($contentTypes as $contentType) {
            if ($contentType instanceof ContentType) {
                $filter = (int)$contentType->getId();
            } elseif (is_numeric($contentType)) {
                $filter = (int)$contentType;
            } else {
                $filter = (string)$contentType;
                $isIdentifier = true;
            }

            $ctFilters[] = $filter;
        }

        if ($isIdentifier) {
            $this->getSelect()
                ->joinLeft(['contenttype' => $this->getTable('blackbird_contenttype')], 'contenttype.ct_id = e.ct_id',
                    '')
                ->where('contenttype.identifier IN (?)', $ctFilters);
        } else {
            $this->addAttributeToFilter(Content::CT_ID, $ctFilters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \Blackbird\ContentManager\Model\Content::class,
            \Blackbird\ContentManager\Model\ResourceModel\Content::class
        );
    }
}
