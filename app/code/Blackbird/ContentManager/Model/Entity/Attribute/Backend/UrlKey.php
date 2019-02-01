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

namespace Blackbird\ContentManager\Model\Entity\Attribute\Backend;

/**
 * Class UrlKey
 *
 * @package Blackbird\ContentManager\Model\Entity\Attribute\Backend
 */
class UrlKey extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Framework\Filter\Factory
     */
    protected $filterFactory;

    /**
     * @param \Magento\Framework\Filter\Factory $filterFactory
     */
    public function __construct(\Magento\Framework\Filter\Factory $filterFactory)
    {
        $this->filterFactory = $filterFactory;
    }

    /**
     * Formatting url key value before save
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws \Zend_Filter_Exception
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        if (!is_null($object->getData($attributeName)) || $object->getData($attributeName) !== false) {
            $urlKey = $object->getData($attributeName);

            if (empty($urlKey)) {
                $urlKey = $object->getName();
            }

            $object->setData($attributeName, $this->formatUrlKey($urlKey));
        }

        return $this;
    }

    /**
     * Formate the string as an url string
     *
     * @param string $str
     * @return string
     * @throws \Zend_Filter_Exception
     */
    public function formatUrlKey($str)
    {
        $removeAccents = $this->filterFactory->createFilter('removeAccents');
        $str = $removeAccents->filter($str);
        $str = preg_replace('#[^0-9a-z/\.]+#i', '-', $str);
        $str = strtolower($str);
        $str = trim($str, '-');

        return $str;
    }
}
