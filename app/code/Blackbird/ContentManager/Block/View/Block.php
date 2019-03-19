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

namespace Blackbird\ContentManager\Block\View;

use Magento\Cms\Model\Block as CmsBlockModel;

/**
 * Class Block
 *
 * @package Blackbird\ContentManager\Block\View
 * @method \Blackbird\ContentManager\Model\Content getContent
 */
class Block extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $_CmsBlockCollectionFactory;

    /**
     * Block constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $cmsBlockCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $cmsBlockCollectionFactory,
        array $data = []
    ) {
        $this->_CmsBlockCollectionFactory = $cmsBlockCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare template
     *
     * @return $this
     */
    public function prepareTemplate()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = 'block';

        // Test applying content/view/"content type"/view/block-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/view/block.phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/view/block.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/' . $type . '.phtml');
            }
        }

        return $this;
    }

    /**
     * Load cms block model
     *
     * @param string $blockId
     * @return \Magento\Cms\Model\Block
     */
    public function getCmsBlockModel($blockId)
    {
        /** @var \Magento\Cms\Model\Block $blockModel */
        $blockModel = $this->_CmsBlockCollectionFactory->create()
            ->addFilter(CmsBlockModel::BLOCK_ID, $blockId)
            ->getFirstItem();

        return $blockModel;
    }

    /**
     * Return an html cms block
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCmsBlockHtml()
    {
        $block = $this->getCmsBlock();
        $result = ($block) ? $block->toHtml() : '';

        return $result;
    }

    /**
     * Return a cms block
     *
     * @return \Magento\Cms\Block\Block
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCmsBlock()
    {
        /** @var \Magento\Cms\Block\Block $block */
        $block = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($this->getLayoutBlock()
            ->getBlockId());

        return $block;
    }
}
