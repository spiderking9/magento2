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

namespace Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\Item;

/**
 * Class CmsBlock
 *
 * @package Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts\Item
 */
class CmsBlock implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $_blockCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * CmsBlock constructor
     *
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->_blockCollectionFactory = $blockCollectionFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get block identifier option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [];

        foreach ($this->_blockCollectionFactory->create() as $block) {
            $storeIds = $block->getResource()->lookupStoreIds($block->getId());
            $stores = [];

            foreach ($storeIds as $storeId) {
                if ($storeId == 0) {
                    $stores[] = __('All Store views');
                } else {
                    $stores[] = $this->_storeManager->getStore($storeId)->getName();
                }
            }

            $label = '(' . __('Identifier') . ': ' . $block->getIdentifier();
            $label .= ', ' . __('Store View') . ' ' . join(', ', $stores) . ') ';
            $label .= $block->getTitle();
            $return[] = ['value' => $block->getId(), 'label' => $label];
        }

        return $return;
    }
}
