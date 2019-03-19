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
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class View
 *
 * @package Blackbird\ContentManager\Block\Content\Widget
 */
class View extends \Magento\Catalog\Block\Product\AbstractProduct
    implements \Magento\Framework\DataObject\IdentityInterface, \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_contentCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts
     */
    protected $_sourceLayouts;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * View constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $sourceLayouts
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\Layouts $sourceLayouts,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_sourceLayouts = $sourceLayouts;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdentities()
    {
        return [
            ContentType::CACHE_TAG . '_' . $this->getContent()->getContentType()->getId(),
            Content::CACHE_TAG . '_' . $this->getContent()->getId(),
        ];
    }

    /**
     * Get current content
     *
     * @return \Blackbird\ContentManager\Model\Content
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContent()
    {
        if (!$this->hasData('content') && $this->hasData('content_id')) {
            $contentCollection = $this->_contentCollectionFactory->create()
                ->addAttributeToFilter(Content::ID, $this->getData('content_id'))
                ->addAttributeToSelect('*');

            if ($contentCollection->count()) {
                $this->setData('content', $contentCollection->getFirstItem());
            }
        }

        return $this->getData('content');
    }

    /**
     * Get the content in HTML format
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentHtml()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $fields = $contentType->getCustomFieldCollection();

        // Layout template, else default template
        if ($this->getChildBlock('content_view_layout') !== false) {
            $html = $this->getChildHtml('content_view_layout');
        } else {
            $html = $content->render('title');

            foreach ($fields as $field) {
                $html .= $content->render($field);
            }
        }

        return $html;
    }

    /**
     * Get processed value
     *
     * @param string $value
     * @return string
     */
    public function getProcessedData($value)
    {
        return $this->_filterProvider->getBlockFilter()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->filter($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $content = $this->getContent();
        $contentType = $content ? $content->getContentType() : null;

        if ($content && $contentType && !$this->getTemplate()) {
            // Applied content layout in cascading
            if ($contentType->getLayout() == 0) {
                // Test applying view-"ID".phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view-' . $content->getId() . '.phtml');

                if (!$this->getTemplateFile()) {
                    // Test applying the overriding content type view.phtml
                    $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view.phtml');

                    if (!$this->getTemplateFile()) {
                        // Applying default view.phtml
                        $this->setTemplate('Blackbird_ContentManager::content/view/default/view.phtml');
                    }
                }
            } else {
                // Test applying view/layout-ID.phtml
                if ($this->_sourceLayouts->layoutExists($contentType->getLayout())) {
                    // Build the layout template dynamically
                    $this->addChild('content_view_layout', \Blackbird\ContentManager\Block\View\Layout::class, [
                        'layout_template' => $this->_sourceLayouts->retrieveLayout($contentType->getLayout()),
                        'content_type' => $contentType,
                        'content' => $content,
                    ]);
                }

                // Applying default view.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view.phtml');
            }
        }

        return $this;
    }
}
