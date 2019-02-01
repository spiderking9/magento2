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
 * Class Block
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Layout\Templates\Item
 */
class Block extends AbstractItem
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/layout/templates/item/block.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\Item\CmsBlock
     */
    protected $_cmsblock;

    /**
     * Block constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\Item\CmsBlock $cmsblock
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\LayoutFieldLabel $labelOptions,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\Item\CmsBlock $cmsblock,
        array $data = []
    ) {
        parent::__construct($context, $labelOptions, $data);
        $this->_cmsblock = $cmsblock;
    }

    /**
     * Return select block from the cms
     *
     * @param string $id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockSelectHtml($id = '')
    {
        /** @var \Magento\Framework\View\Element\Html\Select $select */
        $select = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')->setData([
            'id' => $id,
            'class' => 'select select-contenttype-layout-block-block-id required-entry',
            'label' => __('Select Block'),
            'title' => __('Select Block'),
        ])->setName('layout[item][block][<%- data.id %>][block_id]')->setOptions($this->_cmsblock->toOptionArray());

        return $select->getHtml();
    }
}
