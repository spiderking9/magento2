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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab;

use Blackbird\ContentManager\Model\Content;
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Url
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab
 */
class Url extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\AbstractTab
{
    const DEFAULT_URL = \Blackbird\ContentManager\Api\Data\ContentTypeInterface::DEFAULT_URL;

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('URL');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('URL');
    }

    /**
     * Show the tab or not
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is the tab hidden or not
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('content_');

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $content = $this->_coreRegistry->registry('current_content');

        /** Default URL pattern */

        $fieldset = $form->addFieldset('url_fieldset', ['legend' => __('Default URL pattern')]);

        $fieldset->addField('url_key', 'text', [
            'name' => 'url_key',
            'label' => __('URL Key'),
            'title' => __('URL Key'),
            'required' => true,
            'note' => __('Relative to Web Site Base URL. You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
            ($content && $content->getData(Content::DEFAULT_URL) == '1') ? 'readonly'
                : '' => ($content && $content->getData(Content::DEFAULT_URL) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'regenerate_url',
                'id' => 'regenerate_url',
                'label' => __('Regenerate URL'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_URL) : '0',
                'default' => $contentType->getData(ContentType::DEFAULT_URL),
                'value' => $content ? $content->getData(Content::URL_KEY) : '',
                'parent' => 'url_key',
            ]),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_content_url_prepareform', ['form' => $form]);

        // Manage default value
        $data[Content::URL_KEY] = $contentType->getData(ContentType::DEFAULT_URL);
        if ($content) {
            $data[Content::URL_KEY] = $content->getData(Content::URL_KEY);

            if ($content->getData(Content::DEFAULT_URL) == '1') {
                $data[Content::URL_KEY] = $contentType->getData(ContentType::DEFAULT_URL);
            }
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
