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

namespace Blackbird\ContentManager\Block\Content\Widget;

use Blackbird\ContentManager\Model\Content;
use Magento\Framework\View\Element\Html\Link as HtmlLink;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Link
 */
class Link extends HtmlLink implements BlockInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory
     */
    protected $_contentCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/widget/link/link_block.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare label using passed text as parameter
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabel()
    {
        if (!$this->hasData('anchor_text')) {
            $this->setData('anchor_text', $this->getContent()->getData('title'));
        }

        return $this->getData('anchor_text');
    }

    /**
     * Retrieve the content
     *
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContent()
    {
        if (!$this->hasData('content') && $this->hasData('content_id')) {
            $collection = $this->_contentCollectionFactory->create()
                ->addAttributeToSelect(['url_key', 'title'])
                ->addAttributeToFilter(Content::ID, $this->getData('content_id'));

            if ($collection->count()) {
                $this->setData('content', $collection->getFirstItem());
            }
        }

        return $this->getData('content');
    }

    /**
     * Prepare url using passed id path and return it
     * or return false if path was not found in url rewrites
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHref()
    {
        if (!$this->hasData('href') && $this->getContent()) {
            $this->setData('href', $this->getContent()->getLinkUrl());
        }

        return $this->getData('href');
    }

    /**
     * Render block HTML or return empty string if url can't be prepared
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        return $this->getHref() ? parent::_toHtml() : '';
    }
}
