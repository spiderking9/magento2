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

class Import extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\AbstractTab
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Import/Export Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Import/Export Settings');
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
        $form->setHtmlIdPrefix('content_');

        $content = $this->_coreRegistry->registry('current_content');
        $contentType = $this->_coreRegistry->registry('current_contenttype');

        $fieldset = $form->addFieldset('import_content', ['legend' => __('Import Information')]);

        $fieldset->addField('import_identifier', 'text', [
            'name' => 'import_identifier',
            'label' => __('Import Identifier'),
            'title' => __('Import Identifier'),
            'note' => __('Content Identifier used for the import to know if imported content is a new or is an existing to be update.'),
            'after_element_html' => $this->createRelatedCheckbox([
                'name' => 'use_default_import_identifier',
                'id' => 'use_default_import_identifier',
                'label' => __('Use default Import Identifier'),
                'use_default' => $content ? $content->getData(Content::IMPORT_IDENTIFIER) : '1',
                'default' => $contentType->getData(ContentType::DEFAULT_IMPORT_IDENTIFIER_VALUE),
                'value' => $content ? $content->getData(Content::IMPORT_IDENTIFIER) : '',
                'parent' => 'import_identifier',
            ]),
        ]);

        $data[Content::IMPORT_IDENTIFIER] = $contentType->getData(ContentType::DEFAULT_IMPORT_IDENTIFIER_VALUE);
        if ($content) {
            $data[Content::IMPORT_IDENTIFIER] = $content->getData(Content::IMPORT_IDENTIFIER);
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}