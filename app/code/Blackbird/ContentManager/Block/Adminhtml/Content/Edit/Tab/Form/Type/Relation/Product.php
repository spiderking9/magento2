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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation;

/**
 * Class Product
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation
 */
class Product extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\AbstractType
{
    /**
     * Default template for product relation fields
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit/tab/form/type/relation/product.phtml';

    /**
     * Get source Url for the product widget
     *
     * @return string
     */
    public function getUrlSource($form = false)
    {
        if ($form) {
            return $this->getUrl('contentmanager/product_widget/chooser');
        }

        return $this->getUrl('contentmanager/product_widget/chooser', ['form' => $this->getElement()->getHtmlId()]);
    }
}
