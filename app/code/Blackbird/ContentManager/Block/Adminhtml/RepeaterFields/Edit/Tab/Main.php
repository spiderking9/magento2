<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package        Blackbird_ContentManager
 * @copyright           Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author        Blackbird Team
 * @license        http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Block\Adminhtml\RepeaterFields\Edit\Tab;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;

/**
 * Class Main
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\RepeaterFields\Edit\Tab
 */
class Main extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Main
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Repeater Fields Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Repeater Fields Information');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contenttype_');

        /** Information */

        $fieldset = $form->addFieldset('informations_fieldset', ['legend' => __('Repeater Fields Information')]);

        $fieldset->addField('visibility', 'hidden', [
            'name' => 'visibility',
            'value' => Visibility::REPEATER_FIELD,
        ]);

        $fieldset->addField('title', 'text', [
            'name' => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
            'required' => true,
        ]);

        $fieldset->addField('identifier', 'text', [
            'name' => 'identifier',
            'label' => __('Identifier'),
            'title' => __('Identifier'),
            'class' => 'validate-identifier',
            'required' => true,
        ]);

        $fieldset->addField('default_status', 'select', [
            'name' => 'default_status',
            'label' => __('Default Status'),
            'title' => __('Default Status'),
            'required' => false,
            'values' => $this->_enabledisable->toOptionArray(),
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'tile' => __('Description'),
            'required' => false,
        ]);

        $this->_eventManager->dispatch('adminhtml_block_contentmanager_contenttype_informations_prepareform',
            ['form' => $form]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }
        $this->setForm($form);

        return $this;
    }
}
