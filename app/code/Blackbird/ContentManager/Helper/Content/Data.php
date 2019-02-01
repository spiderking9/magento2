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

namespace Blackbird\ContentManager\Helper\Content;

use Blackbird\ContentManager\Model\Content;

/**
 * Content Data Helper
 *
 * Class Data
 *
 * @package Blackbird\ContentManager\Helper\Content
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_localDate;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $localDate
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\Timezone $localDate,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
    ) {
        parent::__construct($context);
        $this->_localDate = $localDate;
        $this->_contentCollectionFactory = $contentCollectionFactory;
    }

    /**
     * Add CustomField filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType|integer|string $contentType
     * @param \Magento\Eav\Model\Entity\Attribute\AttributeInterface|integer|string|array $attribute
     * @param null|string|array $condition
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentsByCustomField($contentType, $attribute, $condition = null)
    {
        $contentCollection = $this->_contentCollectionFactory->create()
            ->addStoreFilter()
            ->addContentTypeFilter($contentType)
            ->addAttributeToFilter($attribute, $condition);

        return $contentCollection;
    }

    /**
     * Replace {{.*}} patterns in data
     *
     * @param Content $content
     * @param string $value
     * @return array
     */
    public function applyPattern(Content $content, $value)
    {
        $matches = [];
        preg_match_all('/{{([a-zA-Z0-9_\|]*)}}/', (string)$value, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $key => $replacement) {
                $attributeContent = $content->getData($replacement);

                if (preg_match('/\|plain/', $replacement)) {
                    $replacement = str_replace('|plain', '', $replacement);
                    $attributeContent = $this->_getPlainValue($content->getData($replacement));
                } elseif (preg_match('/\|date_short/', $replacement)) {
                    $replacement = str_replace('|date_short', '', $replacement);
                    $attributeContent = $this->_localDate->formatDate(
                        $content->getData($replacement),
                        'short',
                        false
                    );
                } elseif (preg_match('/\|date_medium/', $replacement)) {
                    $replacement = str_replace('|date_medium', '', $replacement);
                    $attributeContent = $this->_localDate->formatDate(
                        $content->getData($replacement),
                        'medium',
                        false
                    );
                } elseif (preg_match('/\|date_long/', $replacement)) {
                    $replacement = str_replace('|date_long', '', $replacement);
                    $attributeContent = $this->_localDate->formatDate(
                        $content->getData($replacement),
                        'long',
                        false
                    );
                } elseif (preg_match('/\|datetime_short/', $replacement)) {
                    $replacement = str_replace('|datetime_short', '', $replacement);
                    $attributeContent = $this->_localDate->formatDateTime(
                        $content->getData($replacement),
                        'short',
                        true
                    );
                } elseif (preg_match('/\|datetime_medium/', $replacement)) {
                    $replacement = str_replace('|datetime_medium', '', $replacement);
                    $attributeContent = $this->_localDate->formatDateTime(
                        $content->getData($replacement),
                        'medium',
                        true
                    );
                } elseif (preg_match('/\|datetime_long/', $replacement)) {
                    $replacement = str_replace('|datetime_long', '', $replacement);
                    $attributeContent = $this->_localDate->formatDateTime(
                        $content->getData($replacement),
                        'long',
                        true
                    );
                } elseif (preg_match('/\|image_url/', $replacement)) {
                    $replacement = str_replace('|image_url', '', $replacement);
                    $attributeContent = $content->getImage($replacement);
                } elseif (preg_match('/\|file_url/', $replacement)) {
                    $replacement = str_replace('|file_url', '', $replacement);
                    $attributeContent = $content->getFile($replacement);
                }

                $value = str_replace($matches[0][$key], $attributeContent, $value);
            }
        }

        return $value;
    }

    /**
     * Get plain value for a content
     *
     * @param string $str
     * @return string
     */
    protected function _getPlainValue($str)
    {
        return strip_tags($str);
    }
}
