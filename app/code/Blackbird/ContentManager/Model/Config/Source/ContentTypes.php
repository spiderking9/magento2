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

namespace Blackbird\ContentManager\Model\Config\Source;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class ContentTypes
 *
 * @package Blackbird\ContentManager\Model\Config\Source
 */
class ContentTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_ctCollectionFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var array
     */
    private $contentTypes = [];

    /**
     * ContentTypes constructor
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $ctCollectionFactory
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $ctCollectionFactory,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->_ctCollectionFactory = $ctCollectionFactory;
        $this->_escaper = $escaper;
    }

    /**
     * Get content type option array
     *
     * @param bool $default
     * @return array
     */
    public function toOptionArray($default = false)
    {
        if (!$this->contentTypes) {
            $collection = $this->_ctCollectionFactory->create()->addFieldToSelect([
                ContentType::TITLE,
                ContentType::IDENTIFIER,
            ])->addFieldToFilter(ContentType::VISIBILITY, ['neq' => Visibility::REPEATER_FIELD]);
            $array = [];

            if ($default) {
                $array[] = [
                    'label' => __('All Content Types'),
                    'value' => 0,
                ];
            }

            foreach ($collection as $contenttype) {
                $array[] = [
                    'label' => $this->_escaper->escapeHtml($contenttype->getTitle()),
                    'value' => $contenttype->getIdentifier(),
                ];
            }

            $this->contentTypes = $array;
        }

        return $this->contentTypes;
    }

    /**
     * Get content type list
     *
     * @param null $visibility
     * @return array
     */
    public function getOptions($visibility = null)
    {
        $collection = $this->_ctCollectionFactory->create()->addFieldToSelect([ContentType::ID, ContentType::TITLE]);
        $array = [];

        if (!is_null($visibility)) {
            $collection->addFieldToFilter(ContentType::VISIBILITY, $visibility);
        }

        foreach ($collection as $contenttype) {
            $array[$contenttype->getCtId()] = $contenttype->getTitle();
        }

        return $array;
    }
}
