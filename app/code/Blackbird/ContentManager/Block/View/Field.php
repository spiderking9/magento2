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

/**
 * Class Field
 *
 * @package Blackbird\ContentManager\Block\View
 * @method \Blackbird\ContentManager\Model\Content getContent
 * @method string getType
 */
class Field extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Field constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->_filterProvider = $filterProvider;
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
        $type = ($this->getType()) ? $this->getType() : 'field';

        // Test applying content/view/"content type"/view/field/"field type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/view/field/"field type".phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/view/field/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/' . $type . '.phtml');
            }
        }

        return $this;
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
}
