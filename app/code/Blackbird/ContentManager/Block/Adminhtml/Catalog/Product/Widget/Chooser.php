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

namespace Blackbird\ContentManager\Block\Adminhtml\Catalog\Product\Widget;

/**
 * Class Chooser
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Catalog\Product\Widget
 */
class Chooser extends \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku
{
    /**
     * Get the current grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('contentmanager/product_widget/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]);
    }
}
