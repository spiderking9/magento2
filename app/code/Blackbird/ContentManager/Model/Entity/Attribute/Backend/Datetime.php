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
 * Class Datetime
 *
 * @package Blackbird\ContentManager\Model\Entity\Attribute\Backend
 */
class Datetime extends \Magento\Eav\Model\Entity\Attribute\Backend\Datetime
{
    /**
     * Formatting date value before save
     *
     * Should set (bool, string) correct type for empty value from html form,
     * necessary for further process, else date string
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws \Exception
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $_formated = $object->getData($attributeName . '_is_formated');

        // We delete a language
        if (is_null($object->getData($attributeName))) {
            return $this;
        }

        if (!$_formated && $object->hasData($attributeName)) {
            try {
                // Blackbird change : adding strtotime() to keep hours data + special cast for european date format
                $value = $this->_localeDate->date($object->getData($attributeName));
                $value = date('Y-m-d g:i A', $value->getTimestamp());
            } catch (\Exception $e) {
                throw new \Exception(__('Invalid date time'));
            }

            if (is_null($value)) {
                $value = $object->getData($attributeName);
            }

            $object->setData($attributeName, $value);
            $object->setData($attributeName . '_is_formated', true);
        }

        return $this;
    }

    //    /*//TODO DATETIME BUG
    //    /*if (empty($date)) {
    //        return null;
    //    }
    //    // unix timestamp given - simply instantiate date object
    //    if (is_scalar($date) && preg_match('/^[0-9]+$/', $date)) {
    //        $date = (new \DateTime())->setTimestamp($date);
    //    } elseif (!($date instanceof \DateTime)) {
    //        // normalized format expecting Y-m-d[ H:i:s]  - time is optional
    //        $date = new \DateTime($date);
    //    }
    //    return $date->format('Y-m-d H:i:s');*/
    //
    //var_dump($this->_localeDate->getDateFormat());
    //var_dump($this->_localeDate->convertConfigTimeToUtc($date));
    //
    //
    //$result = $this->_localeDate->formatDateTime(
    //$this->_localeDate->date($date),
    //\IntlDateFormatter::SHORT,
    //\IntlDateFormatter::SHORT,
    //null,
    //'UTC'
    //);
    //
    //$test = $this->_localeDate->formatDateTime(
    //new \DateTime($date),
    //\IntlDateFormatter::SHORT,
    //\IntlDateFormatter::SHORT,
    //null,
    //'UTC'
    //);
    //
    //
    //
    //    //return parent::formatDate($date);
    //var_dump($date);
    //var_dump(parent::formatDate($date));
    //var_dump(parent::formatDate($this->_localeDate->date($date)->getTimestamp()));
    //var_dump($this->_localeDate->date($date)->getTimestamp());
    //var_dump($result);
    //var_dump($test);
    //var_dump('-------------------------------------------');
    //return parent::formatDate($this->_localeDate->date($date)->getTimestamp());*/
}
