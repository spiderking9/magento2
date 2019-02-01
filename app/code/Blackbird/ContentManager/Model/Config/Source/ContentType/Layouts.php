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

use Magento\Framework\Config\Data;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Layouts
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType
 */
class Layouts implements ArrayInterface
{
    /**
     * @var \Magento\Framework\Config\Data
     */
    private $_layoutsConfig;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Framework\Config\Data $config
     */
    public function __construct(Data $config)
    {
        $this->_layoutsConfig = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $groups = [];

            foreach ($this->_layoutsConfig->get() as $layout) {
                $group = [];
                foreach ($layout['layout'] as $lay) {
                    if ($lay['disabled']) {
                        continue;
                    }
                    $group[] = [
                        'label' => __($lay['label']),
                        'value' => $lay['id'],
                    ];
                }
                if (count($group)) {
                    $groups[] = [
                        'label' => __($layout['label']),
                        'value' => $group,
                        'optgroup-name' => $lay['label'],
                    ];
                }
            }

            $this->options = $groups;
        }

        return $this->options;
    }

    /**
     * Get layout list
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_layoutsConfig->get();
    }

    /**
     * Retrieve the data of the given layout id
     *
     * @param string $layoutId
     * @return array
     */
    public function retrieveLayout($layoutId)
    {
        $layoutData = [];

        // If the layout does not exists
        if (!$this->layoutExists($layoutId)) {
            return $layoutData;
        }

        foreach ($this->_layoutsConfig->get() as $layout) {
            foreach ($layout['layout'] as $lay) {
                if ($lay['disabled']) {
                    continue;
                }

                if ($lay['id'] == $layoutId) {
                    $layoutData = $lay;
                }
            }
        }

        return $layoutData;
    }

    /**
     * Check if a given layout id exists
     *
     * @param string $layoutId
     * @return boolean
     */
    public function layoutExists($layoutId)
    {
        $exists = false;

        foreach ($this->_layoutsConfig->get() as $layout) {
            foreach ($layout['layout'] as $lay) {
                if ($lay['disabled']) {
                    continue;
                }

                if ($lay['id'] == $layoutId) {
                    $exists = true;
                }
            }
        }

        return $exists;
    }
}
