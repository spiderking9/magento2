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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item;

/**
 * Class AbstractItem
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item
 */
abstract class AbstractItem
    extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type\AbstractType
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel
     */
    protected $_labelOptions;

    /**
     * AbstractItem constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_labelOptions = $labelOptions;
    }

    /**
     * Return select 'how to show the label'
     *
     * @param string $id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelSelectHtml($id = '')
    {
        /** @var \Magento\Framework\View\Element\Html\Select $select */
        $select = $this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Html\Select')
            ->setData([
                'id' => $id,
                'class' => 'select select-contenttype-layout-block-label required-entry',
                'label' => __('Label Option'),
                'title' => __('Label Option'),
            ])
            ->setName('layout[item][<%- data.item %>][<%- data.id %>][label]')
            ->setOptions($this->_labelOptions->toOptionArray());

        return $select->getHtml();
    }
}
