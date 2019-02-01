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
 * Class Content
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * Content constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Extra Content');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Extra Content');
    }

    /**
     * Can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
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
        $form->setHtmlIdPrefix('contentlist_');
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['add_directives' => true, 'tab_id' => $this->getTabId()]);

        /** Content */

        $fieldset = $form->addFieldset('informations_fieldset', ['legend' => __('Extra Content')]);

        $fieldset->addField(ContentListData::TEXT_BEFORE, 'editor', [
            'name' => ContentListData::TEXT_BEFORE,
            'label' => __('Text Before'),
            'tile' => __('Text Before'),
            'required' => false,
            'config' => $wysiwygConfig,
        ]);

        $fieldset->addField(ContentListData::TEXT_AFTER, 'editor', [
            'name' => ContentListData::TEXT_AFTER,
            'label' => __('Text After'),
            'tile' => __('Text After'),
            'required' => false,
            'config' => $wysiwygConfig,
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contentlist_content_prepareform',
            ['form' => $form]);

        $contentList = $this->_coreRegistry->registry('current_contentlist');
        if ($contentList) {
            $form->setValues($contentList->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
