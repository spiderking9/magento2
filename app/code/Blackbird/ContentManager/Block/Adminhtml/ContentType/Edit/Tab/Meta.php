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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab;

/**
 * Class Meta
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
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
     * @return string
     */
    public function getTabLabel()
    {
        return __('Default Meta Data');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Default Meta Data');
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
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
        $form->setHtmlIdPrefix('contenttype_');

        /** Default Meta Tags */

        $fieldset = $form->addFieldset('meta_fieldset', ['legend' => __('Default Meta Tags')]);

        $fieldset->addField('message_notice_meta', 'hidden', [])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\Messages', 'message_notice_meta',
                ['data' => $messageData]));

        $fieldset->addField('meta_title', 'text', [
            'name' => 'meta_title',
            'label' => __('Meta Title'),
            'title' => __('Meta Title'),
        ]);

        $fieldset->addField('meta_description', 'textarea', [
            'name' => 'meta_description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        $fieldset->addField('meta_keywords', 'textarea', [
            'name' => 'meta_keywords',
            'label' => __('Keywords'),
            'title' => __('Keywords'),
        ]);

        $fieldset->addField('meta_robots', 'select', [
            'name' => 'meta_robots',
            'label' => __('Robots'),
            'title' => __('Robots'),
            'required' => true,
            'values' => $this->_robotsConfig->toOptionArray(),
        ]);

        /** Default Open Graph */

        $fieldset = $form->addFieldset('og_fieldset', ['legend' => __('Default Open Graph')]);

        $fieldset->addField('message_notice_og', 'hidden', [])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\Messages', 'message_notice_og',
                ['data' => $messageData]));

        $fieldset->addField('og_title', 'text', [
            'name' => 'og_title',
            'label' => __('Title'),
            'title' => __('Title'),
        ]);

        $fieldset->addField('og_description', 'textarea', [
            'name' => 'og_description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        $fieldset->addField('og_url', 'text', [
            'name' => 'og_url',
            'label' => __('URL'),
            'title' => __('URL'),
            'class' => 'validate-url',
        ]);

        $fieldset->addField('og_type', 'text', [
            'name' => 'og_type',
            'label' => __('Type'),
            'title' => __('Type'),
        ]);

        $fieldset->addField('og_image', 'text', [
            'name' => 'og_image',
            'label' => __('Image'),
            'title' => __('Image'),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_meta_prepareform',
            ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
