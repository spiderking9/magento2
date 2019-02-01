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

use Blackbird\ContentManager\Api\Data\ContentType\CustomFieldInterface;

/**
 * Class CustomFields
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType
 */
class CustomFields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory
     */
    protected $_customFieldCollectionFactory;

    /**
     * CustomFields constructor
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
     */
    public function __construct(
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
    ) {
        $this->_customFieldCollectionFactory = $customFieldCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($withStaticAttribute = true)
    {
        $collection = $this->_customFieldCollectionFactory->create()
            ->addContentTypeTitleToResult()
            ->addTitleToResult()
            ->addOrder('content_type_title', 'ASC');
        $return = [];

        //todo refactor + improve
        if ($withStaticAttribute) {
            $return[] = [
                'value' => 'title',
                'label' => __('Page Title (All Content Types)'),
            ];
            $return[] = [
                'value' => 'created_at',
                'label' => __('Created At (All Content Types)'),
            ];
            $return[] = [
                'value' => 'updated_at',
                'label' => __('Updated At (All Content Types)'),
            ];
        }

        foreach ($collection as $customField) {
            $return[] = [
                'value' => $customField->getIdentifier(),
                'label' => $customField->getContentTypeTitle() . ' - ' . $customField->getTitle() . ' [' . $customField->getType() . ']',
            ];
        }

        return $return;
    }

    /**
     * Get option array by content type
     *
     * @param int $contentTypeId
     * @return array
     */
    public function toOptionArrayByContentType($contentTypeId)
    {
        $return = [];
        $collection = $this->_customFieldCollectionFactory->create()
            ->addFieldToFilter(CustomFieldInterface::CT_ID, $contentTypeId)
            ->addTitleToResult()
            ->addOrder(CustomFieldInterface::SORT_ORDER, 'asc')
            ->addOrder(CustomFieldInterface::FIELDSET_ID, 'asc');

        foreach ($collection as $customField) {
            $return[] = ['value' => $customField->getId(), 'label' => $customField->getTitle()];
        }

        return $return;
    }

    /**
     * Get array by content type id
     *
     * @param int $contentTypeId
     * @return array
     */
    public function toArray($contentTypeId = null)
    {
        $return = [];

        $collection = $this->_customFieldCollectionFactory->create()
            ->addTitleToResult()
            ->setOrder(CustomFieldInterface::SORT_ORDER, 'asc')
            ->setOrder(CustomFieldInterface::FIELDSET_ID, 'asc');

        if ($contentTypeId) {
            $collection->addFieldToFilter(CustomFieldInterface::CT_ID, $contentTypeId);
        } else {
            $collection->addContentTypeTitleToResult()->addOrder('content_type_title', 'ASC');
        }

        foreach ($collection as $customField) {
            $label = $customField->getTitle() . ' [' . $customField->getType() . ']';
            $return[] = [
                'value' => $customField->getId(),
                'label' => $contentTypeId ? $customField->getContentTypeTitle() . ' - ' . $label : $label,
                'identifier' => $customField->getIdentifier(),
                'type' => $customField->getType(),
            ];
        }

        return $return;
    }

    /**
     * Retrieve all identifiers
     *
     * @param array|string $identifier
     * @param int $excludeFieldId
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCustomFieldsByIdentifiers($identifier = [], $excludeFieldId = null)
    {
        $collection = $this->_customFieldCollectionFactory->create()->addFieldToFilter('identifier', $identifier);

        if ($excludeFieldId) {
            $collection = $collection->addFieldToFilter('option_id', ['neq' => $excludeFieldId]);
        }

        return $collection;
    }
}
