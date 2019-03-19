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

use Blackbird\ContentManager\Model\Content as ContentModel;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class Content extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Return the collection of the contents
     *
     * @param array $attributes
     * @return \Blackbird\ContentManager\Model\ResourceModel\Content\Collection
     */
    public function getContentCollection(array $attributes)
    {
        return $this->getContent()
            ->getContentCollection($this->getIdentifier(), array_merge($attributes, ['title', 'url_key']))
            ->addAttributeToFilter(ContentModel::STATUS, 1);
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
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/content/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/content/"content type.phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/content/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/content/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/content/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }
}
