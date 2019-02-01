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

/**
 * Image grid column
 *
 * Class Image
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer
 */
class Image extends AbstractRenderer
{
    /**
     * Render row store views flags
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '';
        $data = $row->getData();
        $identifier = $this->getColumn()->getIndex();
        $content = $this->getContent($data['entity_id'], [$identifier]);
        $imageUrl = $content->getImage($identifier, 50, 50, true, true);
        $imageLink = $content->getImage($identifier);

        if ($row->hasData($identifier)) {
            $html = '<a href="' . $imageLink . '" target="_blank"><img src="' . $imageUrl . '" class="admin__control-field-image" alt="' . $row->getData($identifier) . '" /></a>';
        }

        return $html;
    }
}
