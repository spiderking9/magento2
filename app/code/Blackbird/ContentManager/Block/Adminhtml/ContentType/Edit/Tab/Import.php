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

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Import extends Generic implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Import/Export Contents Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Import/Export Contents Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
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
        $form->setHtmlIdPrefix('contenttype_');

        $fieldset = $form->addFieldset('import_content', ['legend' => __('Import Information')]);

        // Notice message
        $messageData = [
            'messages' => [
                [
                    'type' => 'notice',
                    'message' => __('The following fields are used to manage the uniqueness of contents for the import' . 'and export actions.<br />The identifier name is the column where the identifier value is in the ' . 'export file. It\'s also used to check if a given content should be updated or created.'),
                ],
            ],
        ];
        $fieldset->addField('message_notice_ie', 'hidden', [])->setRenderer($this->getLayout()
            ->createBlock('Blackbird\ContentManager\Block\Adminhtml\Messages', 'message_notice_ie',
                ['data' => $messageData]));

        $fieldset->addField('default_import_identifier_name', 'text', [
            'name' => 'default_import_identifier_name',
            'label' => __('Contents Identifier Name'),
            'title' => __('Contents Identifier Name'),
            'required' => true,
            'note' => __('Default name which specify your content identifier in the import/export action.'),
        ]);

        $fieldset->addField('default_import_identifier_value', 'text', [
            'name' => 'default_import_identifier_value',
            'label' => __('Contents Default Identifier Value'),
            'title' => __('Contents Default Identifier Value'),
            'note' => __('Relative to Web Site Base URL. You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ]);

        $contentType = $this->_coreRegistry->registry('current_contenttype');
        if ($contentType) {
            $form->setValues($contentType->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}