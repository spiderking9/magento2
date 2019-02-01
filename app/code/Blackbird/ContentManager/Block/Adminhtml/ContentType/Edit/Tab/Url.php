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

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility as VisibilitySource;
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Url
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Url extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var VisibilitySource
     */
    private $visibilitySource;

    /**
     * Url constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param VisibilitySource $visibilitySource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        VisibilitySource $visibilitySource,
        array $data = []
    ) {
        $this->visibilitySource = $visibilitySource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

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
     * Can show tab or not
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
        parent::_prepareForm();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        /** Default URL pattern */

        $fieldset = $form->addFieldset('url_fieldset', ['legend' => __('Default URL pattern')]);

        $fieldset->addField('visibility', 'select', [
            'name' => 'visibility',
            'label' => __('Visibility'),
            'title' => __('Visibility'),
            'required' => true,
            'note' => __('Select the visibility strategy of your Content Type.'),
            'values' => $this->visibilitySource->toOptionArray(),
        ]);

        $fieldset->addField('default_url', 'text', [
            'name' => 'default_url',
            'label' => __('URL Key'),
            'title' => __('URL Key'),
            'note' => __('Relative to Web Site Base URL. You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_url_prepareform', ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');

        $data = [];
        if ($contentType) {
            $data = $contentType->getData();
        }

        if (!isset($data[ContentType::DEFAULT_URL]) || empty($data[ContentType::DEFAULT_URL])) {
            $data[ContentType::DEFAULT_URL] = '{{title|plain}}';
        }

        $form->setValues($data);
        $this->setForm($form);

        return $this;
    }
}
