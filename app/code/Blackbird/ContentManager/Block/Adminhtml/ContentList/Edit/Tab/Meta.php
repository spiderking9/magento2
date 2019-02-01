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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * Class Meta
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Get tab label
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
     * Can show tab or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
     *
     * @return bool
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
        // Notice message
        $messageData = [
            'messages' => [
                [
                    'type' => 'notice',
                    'message' => __('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
                ],
            ],
        ];

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contentlist_');

        /** Meta Tags */

        $fieldset = $form->addFieldset('meta_fieldset', ['legend' => __('Meta Tags')]);

        $fieldset->addField('message_notice_meta', 'hidden', [])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\Messages', 'message_notice_meta',
                ['data' => $messageData]));

        $fieldset->addField(ContentListData::META_TITLE, 'text', [
            'name' => 'meta_title',
            'label' => __('Meta Title'),
            'title' => __('Meta Title'),
        ]);

        $fieldset->addField(ContentListData::META_DESCRIPTION, 'textarea', [
            'name' => 'meta_description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        $fieldset->addField(ContentListData::META_KEYWORDS, 'textarea', [
            'name' => 'meta_keywords',
            'label' => __('Keywords'),
            'title' => __('Keywords'),
        ]);

        $fieldset->addField(ContentListData::META_ROBOTS, 'select', [
            'name' => 'meta_robots',
            'label' => __('Robots'),
            'title' => __('Robots'),
            'required' => true,
            'values' => $this->_robotsConfig->toOptionArray(),
        ]);

        /** Open Graph */

        $fieldset = $form->addFieldset('og_fieldset', ['legend' => __('Open Graph')]);

        $fieldset->addField('message_notice_og', 'hidden', [])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\Messages', 'message_notice_og',
                ['data' => $messageData]));

        $fieldset->addField(ContentListData::OG_TITLE, 'text', [
            'name' => 'og_title',
            'label' => __('Title'),
            'title' => __('Title'),
        ]);

        $fieldset->addField(ContentListData::OG_DESCRIPTION, 'textarea', [
            'name' => 'og_description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        $fieldset->addField(ContentListData::OG_URL, 'text', [
            'name' => 'og_url',
            'label' => __('URL'),
            'title' => __('URL'),
            'class' => 'validate-url',
        ]);

        $fieldset->addField(ContentListData::OG_TYPE, 'text', [
            'name' => 'og_type',
            'label' => __('Type'),
            'title' => __('Type'),
        ]);

        $fieldset->addField(ContentListData::OG_IMAGE, 'text', [
            'name' => 'og_image',
            'label' => __('Image'),
            'title' => __('Image'),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contentlist_meta_prepareform',
            ['form' => $form]);

        $contentList = $this->_coreRegistry->registry('current_contentlist');
        if ($contentList) {
            $form->setValues($contentList->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
