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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Import\Tab;

use Blackbird\ContentManager\Model\ContentType;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Form
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Import\Tab
 */
class Form extends Generic implements TabInterface
{
    /**
     * @var ContentType
     */
    protected $contentTypeModel;

    /**
     * Import Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Blackbird\ContentManager\Model\ContentType $contentTypeModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        ContentType $contentTypeModel,
        array $data = []
    ) {
        $this->contentTypeModel = $contentTypeModel;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Import Content');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Import Content');
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

        $fieldset = $form->addFieldset('import_content', ['legend' => __('Import Content')]);

        $fieldset->addField('file', 'file', [
            'name' => 'file',
            'label' => __('File'),
            'title' => __('File'),
            'note' => __('You can import CSV file. Previously exported from the content type edit page. Useful to import/export content from local to remote server.'),
            'required' => true,
        ]);

        $fieldset->addField('import_identifier', 'text', [
            'name' => 'import_identifier',
            'label' => __('Import Identifier Column Name'),
            'title' => __('Import Identifier Column Name'),
            'value' => $this->_getDefaultImportIdentifier(),
            'note' => __('Column name in your CSV which specify your content identifier.'),
            'required' => true,
        ]);

        $fieldset->addField('stop_at_error', 'checkbox', [
            'name' => 'stop_at_error',
            'label' => __('Stop Import at Error'),
            'title' => __('Stop Import at Error'),
            'note' => __('If check, import will stop at error and all saved contents will be rollback, else, import will continue for all content and return you a CSV file with none imported content (caused by an error) with a details about errors.'),
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get the default import identifier from the current content type
     *
     * @return string
     */
    protected function _getDefaultImportIdentifier()
    {
        $ctId = $this->getRequest()->getParam('ct_id');

        $contentType = $this->contentTypeModel->load($ctId);

        return $contentType->getDefaultImportIdentifierName();
    }
}
