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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer;

use Blackbird\ContentManager\Api\Data\ContentInterface as Content;

/**
 * StoreView grid column
 *
 * Class Store
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer
 */
class Store extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * Store constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $systemStore, $data);
    }

    /**
     * Render row store views
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $content = $this->getContent($row->getData('entity_id'));
        $origStores = [];
        foreach ($content->getStores() as $store) {
            $origStores[] = $store->getId();
        }
        $row->setData($this->getColumn()->getIndex(), $origStores);

        return parent::render($row);
    }

    /**
     * Retrieve the content from the collection
     *
     * @param int $contentId
     * @return \Blackbird\ContentManager\Model\Content|null
     */
    protected function getContent($contentId)
    {
        $content = null;
        $collection = $this->_contentCollectionFactory->create()->addFieldToFilter(Content::ID, $contentId);

        if ($collection->count()) {
            /** @var \Blackbird\ContentManager\Model\Content $content */
            $content = $collection->getFirstItem();
        }

        return $content;
    }
}
