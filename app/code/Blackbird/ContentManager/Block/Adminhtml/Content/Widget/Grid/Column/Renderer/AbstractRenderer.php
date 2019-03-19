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
 * Class AbstractRenderer
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer
 */
abstract class AbstractRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * AbstractRenderer constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the content from the collection
     *
     * @param int $contentId
     * @param array $attributes
     * @return \Blackbird\ContentManager\Model\Content|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getContent($contentId, $attributes = [])
    {
        $content = null;
        $collection = $this->_contentCollectionFactory->create()->addAttributeToSelect(array_merge([
            'title',
            'status',
            'url_key',
        ], $attributes))->addFieldToFilter(Content::ID, $contentId);

        if ($collection->count()) {
            /** @var \Blackbird\ContentManager\Model\Content $content */
            $content = $collection->getFirstItem();
        }

        return $content;
    }
}
