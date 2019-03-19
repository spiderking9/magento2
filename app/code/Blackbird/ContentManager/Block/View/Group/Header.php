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

namespace Blackbird\ContentManager\Block\View\Group;

/**
 * Class Header
 *
 * @package Blackbird\ContentManager\Block\View\Group
 */
class Header extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Prepare template
     *
     * @return \Magento\Catalog\Block\Product\AbstractProduct
     */
    public function prepareTemplate()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = 'header';

        // Test applying content/view/"content type"/group/header-ID.phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/group/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/group/header.phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/group/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/group/header.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/group/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }

}
