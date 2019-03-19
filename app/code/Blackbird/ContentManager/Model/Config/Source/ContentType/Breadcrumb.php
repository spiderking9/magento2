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
 * Class Breadcrumb
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType
 */
class Breadcrumb implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory
     */
    protected $_customFieldCollectionFactory;

    /**
     * @var int
     */
    protected $contentTypeId;

    /**
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
     */
    public function __construct(
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory $customFieldCollectionFactory
    ) {
        $this->_customFieldCollectionFactory = $customFieldCollectionFactory;
    }

    /**
     * Get breadcrumbs option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            ['value' => '', 'label' => __('No Breadcrumb')],
            ['value' => 'title', 'label' => __('Page Title')],
        ];

        if ($this->getContentTypeId() !== null) {
            $collection = $this->_customFieldCollectionFactory->create()
                ->addFieldToFilter(CustomFieldInterface::CT_ID, $this->getContentTypeId())
                ->addTitleToResult()
                ->addOrder(CustomFieldInterface::SORT_ORDER, 'asc')
                ->addOrder(CustomFieldInterface::FIELDSET_ID, 'asc');

            foreach ($collection as $item) {
                $return[] = [
                    'label' => __($item->getTitle()),
                    'value' => $item->getIdentifier(),
                ];
            }
        }

        return $return;
    }

    /**
     * Get content type id
     *
     * @return int
     */
    public function getContentTypeId()
    {
        return $this->contentTypeId;
    }

    /**
     * Set content type id
     *
     * @param int $contentTypeId
     */
    public function setContentTypeId($contentTypeId)
    {
        if (is_numeric($contentTypeId)) {
            $this->contentTypeId = $contentTypeId;
        }
    }
}
