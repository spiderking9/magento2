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

namespace Blackbird\ContentManager\Plugin\Sitemap;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentList;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory as ContentTypeCollectionFactory;
use Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory as ContentCollectionFactory;
use Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory as ContentListCollectionFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Sitemap\Model\Sitemap as ModelSitemap;

/**
 * Class Sitemap
 */
class Sitemap
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    private $contentTypeCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    private $contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    private $contentListCollectionFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        ContentTypeCollectionFactory $contentTypeCollectionFactory,
        ContentCollectionFactory $contentCollectionFactory,
        ContentListCollectionFactory $contentListCollectionFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->contentTypeCollectionFactory = $contentTypeCollectionFactory;
        $this->contentCollectionFactory = $contentCollectionFactory;
        $this->contentListCollectionFactory = $contentListCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param \Magento\Sitemap\Model\Sitemap $subject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterCollectSitemapItems(ModelSitemap $subject)
    {
        $contentTypeCollection = $this->contentTypeCollectionFactory->create()
            ->addFieldToSelect([ContentType::SITEMAP_FREQUENCY, ContentType::SITEMAP_PRIORITY])
            ->addFieldToFilter(ContentType::VISIBILITY, Visibility::VISIBLE)
            ->addFieldToFilter(ContentType::SITEMAP_ENABLE, 1);

        $contentTypeIds = $contentTypeCollection->getAllIds();

        $contentCollection = $this->contentCollectionFactory->create()
            ->addStoreFilter($subject->getStoreId())
            ->addIsVisibleFilter()
            ->addAttributeToSelect([Content::URL_KEY, Content::UPDATED_AT])
            ->addAttributeToFilter(Content::CT_ID, $contentTypeIds);

        $contentListCollection = $this->contentListCollectionFactory->create()
            ->addFieldToSelect([ContentList::URL_KEY])
            ->addFieldToFilter(ContentList::CT_ID, $contentTypeIds)
            ->addFieldToFilter(ContentList::STATUS, 1);

        /** @var ContentType $contentType */
        foreach ($contentTypeCollection as $contentType) {
            $subject->addSitemapItem(
                $this->dataObjectFactory->create([
                    'changefreq' => $contentType->getData(ContentType::SITEMAP_FREQUENCY),
                    'priority' => $contentType->getData(ContentType::SITEMAP_PRIORITY),
                    'collection' => array_merge(
                        $contentCollection->getItemsByColumnValue(Content::CT_ID, $contentType->getId()),
                        $contentListCollection->getItemsByColumnValue(ContentList::CT_ID, $contentType->getId())
                    ),
                ])
            );
        }
    }
}
