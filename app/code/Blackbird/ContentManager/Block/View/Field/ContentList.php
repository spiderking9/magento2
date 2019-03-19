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

namespace Blackbird\ContentManager\Block\View\Field;

use Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class ContentList extends Template
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory
     */
    protected $contentListCollectionFactory;

    /**
     * ContentList constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentList\CollectionFactory $contentListCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $contentListCollectionFactory,
        array $data = []
    ) {
        $this->contentListCollectionFactory = $contentListCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get all content list of the current content
     *
     * @return \Blackbird\ContentManager\Model\ResourceModel\ContentList\Collection
     */
    public function getContentLists()
    {
        return $this->contentListCollectionFactory->create()
            ->addFieldToFilter(
                \Blackbird\ContentManager\Model\ContentList::ID,
                ['in' => $this->getContent()->getDataAsArray($this->getIdentifier())]
            );
    }

    /**
     * @todo move to abstract generic class
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = $this->getType();

        // Test applying content/view/"content type"/field/content/"content type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/content-list/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/content/"content type.phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/content-list/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/content/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/content-list/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }
}