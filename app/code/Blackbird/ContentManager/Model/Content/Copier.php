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

namespace Blackbird\ContentManager\Model\Content;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentFactory;
use Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory;
use Blackbird\ContentManager\Model\ContentType\CustomField;

/**
 * Class Copier
 *
 * @package Blackbird\ContentManager\Model\Content
 */
class Copier
{
    /**
     * @var \Blackbird\ContentManager\Model\ContentFactory
     */
    protected $_contentFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Blackbird\ContentManager\Model\ContentFactory $contentFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $collectionFactory
     */
    public function __construct(
        ContentFactory $contentFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->_contentFactory = $contentFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Create content duplicate
     *
     * @param \Blackbird\ContentManager\Model\Content $content
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Exception
     */
    public function copy(\Blackbird\ContentManager\Model\Content $content)
    {
        /** @var \Blackbird\ContentManager\Model\Content $duplicate */
        $duplicate = $this->_contentFactory->create();

        foreach ($content->getStoreIds() as $storeId) {
            $content->setStoreId($storeId);
            $content = $content->load($content->getId());
            $duplicateId = $duplicate->getId();

            // Duplicate the content
            $duplicate->setData($content->getData());
            $duplicate->setStatus(0);
            $duplicate->setId($duplicateId);
            $duplicate->isObjectCopied(true);

            // Duplicate the repeater fields
            $repeaterFields = $content->getContentType()->getCustomFieldCollection()->addFieldToFilter(CustomField::TYPE, 'repeater_fields');
            /** @var CustomField $repeaterField */
            foreach ($repeaterFields as $repeaterField) {
                $contents = $this->collectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(Content::ID, $content->getDataAsArray($repeaterField->getIdentifier()));
                $newContents = [];
                foreach ($contents as $subContent) {
                    $newContents[] = $this->copy($subContent)->getId();
                }
                $duplicate->setData($repeaterField->getIdentifier(), $newContents);
            }

            if ($duplicate->isObjectNew()) {
                $duplicate->setCreatedAt(null);
                $duplicate->setUpdatedAt(null);
            }

            // Generated a new url key
            $isDuplicateSaved = false;
            do {
                $urlKey = $duplicate->getUrlKey();
                $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches) ? $matches[1] . '-' . ($matches[2] + 1)
                    : $urlKey . '-1';
                $duplicate->setUrlKey($urlKey);
                try {
                    $duplicate->save();
                    $isDuplicateSaved = true;
                } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                    // Silence is golden
                }
            } while (!$isDuplicateSaved);
        }

        return $duplicate;
    }
}
