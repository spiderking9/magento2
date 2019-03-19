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
 * Class Meta
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab
 */
class Meta extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\AbstractTab
{
    /**
     * @var \Magento\Config\Model\Config\Source\Design\Robots
     */
    protected $_robotsConfig;

    /**
     * Meta constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Design\Robots $robotsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Design\Robots $robotsConfig,
        array $data = []
    ) {
        $this->_robotsConfig = $robotsConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label name
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Search Engine Optimization');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Search Engine Optimization');
    }

    /**
     * Show tab or not in adminhtml
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden or not
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

        /** Default Meta Tags */

        $fieldset = $form->addFieldset('meta_fieldset', ['legend' => __('Meta tags')]);

        $fieldset->addField('meta_title', 'text', [
            'name' => 'meta_title',
            'label' => __('Meta Title'),
            'title' => __('Meta Title'),
            (!$content || $content->getData(Content::DEFAULT_META_TITLE) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_META_TITLE) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_meta_title',
                'id' => 'use_default_meta_title',
                'label' => __('Use default Meta Title'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_META_TITLE) : '1',
                'default' => $contentType->getData(ContentType::META_TITLE),
                'value' => $content ? $content->getData(Content::META_TITLE) : '',
                'parent' => 'meta_title',
            ]),
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Meta Description'),
            'title' => __('Meta Description'),
            (!$content || $content->getData(Content::DEFAULT_DESCR) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_DESCR) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_description',
                'id' => 'use_default_description',
                'label' => __('Use default Description'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_DESCR) : '1',
                'default' => $contentType->getData(ContentType::META_DESCRIPTION),
                'value' => $content ? $content->getData(Content::META_DESCRIPTION) : '',
                'parent' => 'description',
            ]),
        ]);

        $fieldset->addField('keywords', 'textarea', [
            'name' => 'keywords',
            'label' => __('Meta Keywords'),
            'title' => __('Meta Keywords'),
            (!$content || $content->getData(Content::DEFAULT_KEYWORDS) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_KEYWORDS) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_keywords',
                'id' => 'use_default_keywords',
                'label' => __('Use default Keywords'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_KEYWORDS) : '1',
                'default' => $contentType->getData(ContentType::META_KEYWORDS),
                'value' => $content ? $content->getData(Content::META_KEYWORDS) : '',
                'parent' => 'keywords',
            ]),
        ]);

        $fieldset->addField('robots', 'select', [
            'name' => 'robots',
            'label' => __('Meta Robots'),
            'title' => __('Meta Robots'),
            'required' => true,
            'values' => $this->_robotsConfig->toOptionArray(),
        ]);

        /** Default Open Graph */

        $fieldset = $form->addFieldset('og_fieldset', ['legend' => __('Open Graph')]);

        $fieldset->addField('og_title', 'text', [
            'name' => 'og_title',
            'label' => __('OG Title'),
            'title' => __('OG Title'),
            (!$content || $content->getData(Content::DEFAULT_OG_TITLE) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_OG_TITLE) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_og_title',
                'id' => 'use_default_og_title',
                'label' => __('Use default OG Title'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_OG_TITLE) : '1',
                'default' => $contentType->getData(ContentType::OG_TITLE),
                'value' => $content ? $content->getData(Content::OG_TITLE) : '',
                'parent' => 'og_title',
            ]),
        ]);

        $fieldset->addField('og_description', 'textarea', [
            'name' => 'og_description',
            'label' => __('OG Description'),
            'title' => __('OG Description'),
            (!$content || $content->getData(Content::DEFAULT_OG_DESCR) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_OG_DESCR) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_og_description',
                'id' => 'use_default_og_description',
                'label' => __('Use default OG Description'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_OG_DESCR) : '1',
                'default' => $contentType->getData(ContentType::OG_DESCRIPTION),
                'value' => $content ? $content->getData(Content::OG_DESCRIPTION) : '',
                'parent' => 'og_description',
            ]),
        ]);

        $fieldset->addField('og_url', 'text', [
            'name' => 'og_url',
            'label' => __('OG URL'),
            'title' => __('OG URL'),
            'class' => 'validate-url',
            (!$content || $content->getData(Content::DEFAULT_OG_URL) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_OG_URL) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_og_url',
                'id' => 'use_default_og_url',
                'label' => __('Use default OG Url'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_OG_URL) : '1',
                'default' => $contentType->getData(ContentType::OG_URL),
                'value' => $content ? $content->getData(Content::OG_URL) : '',
                'parent' => 'og_url',
            ]),
        ]);

        $fieldset->addField('og_type', 'text', [
            'name' => 'og_type',
            'label' => __('OG Type'),
            'title' => __('OG Type'),
            (!$content || $content->getData(Content::DEFAULT_OG_TYPE) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_OG_TYPE) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_og_type',
                'id' => 'use_default_og_type',
                'label' => __('Use default OG Type'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_OG_TYPE) : '1',
                'default' => $contentType->getData(ContentType::OG_TYPE),
                'value' => $content ? $content->getData(Content::OG_TYPE) : '',
                'parent' => 'og_type',
            ]),
        ]);

        $fieldset->addField('og_image', 'text', [
            'name' => 'og_image',
            'label' => __('OG Image'),
            'title' => __('OG Image'),
            (!$content || $content->getData(Content::DEFAULT_OG_IMAGE) == '1') ? 'readonly'
                : '' => (!$content || $content->getData(Content::DEFAULT_OG_IMAGE) == '1') ? true : '',
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_og_image',
                'id' => 'use_default_og_image',
                'label' => __('Use default OG Image'),
                'use_default' => $content ? $content->getData(Content::DEFAULT_OG_IMAGE) : '1',
                'default' => $contentType->getData(ContentType::OG_IMAGE),
                'value' => $content ? $content->getData(Content::OG_IMAGE) : '',
                'parent' => 'og_image',
            ]),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_content_meta_prepareform', ['form' => $form]);

        $form->setValues($this->getValues($content, $contentType));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve default values for the meta tags and merge with the content's data
     *
     * @param Content $content
     * @param ContentType $contentType
     * @return array
     */
    protected function getValues($content, $contentType)
    {
        $values = [];
        $keys = [
            'meta_title' => '',
            'description' => 'meta_',
            'keywords' => 'meta_',
            'robots' => 'meta_',
            'og_title' => '',
            'og_url' => '',
            'og_description' => '',
            'og_image' => '',
            'og_type' => '',
        ];

        if ($content) {
            foreach ($keys as $key => $prefix) {
                // Update value if content type or attribute has been updated
                if ($content->getData('use_default_' . $key) == '1') {
                    $values[$key] = $contentType->getData($prefix . $key);
                } else {
                    $values[$key] = $content->getData($key);
                }
            }
        } else {
            foreach ($keys as $key => $prefix) {
                $values[$key] = $contentType->getData($prefix . $key);
            }
        }

        return $values;
    }
}
