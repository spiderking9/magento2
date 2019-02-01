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

namespace Blackbird\ContentManager\Block\Adminhtml;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Block\Adminhtml
 */
class Content extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Content constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Url to the creation page of a content
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', ['_current' => true]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $contentType = $this->_coreRegistry->registry('current_contenttype');
        $contentTitle = ' ' . $contentType->getTitle();
        $this->_controller = 'adminhtml_content';
        $this->_addButtonLabel = __('Add Content') . $contentTitle;

        $this->addButton('export_all_contents', [
            'label' => __('Export All Contents') . $contentTitle,
            'id' => 'export_button',
            'onclick' => 'setLocation(\'' . $this->getExportAllUrl($contentType->getCtId()) . '\')',
        ]);


        $this->addButton('import_content', [
            'label' => __('Import Contents') . $contentTitle,
            'class' => 'action-secondary',
            'onclick' => 'setLocation(\'' . $this->getImportUrl($contentType->getCtId()) . '\')',
        ]);

        parent::_construct();
    }

    /**
     * Url for mass export to export all contents form the current content type
     *
     * @param $ctId
     * @return string
     */
    public function getExportAllUrl($ctId)
    {
        return $this->getUrl('*/*/massExport', ['export_all' => true, 'ct_id' => $ctId]);
    }

    /**
     * Url to the contents import page
     *
     * @param $ctId
     * @return string
     */
    public function getImportUrl($ctId)
    {
        return $this->getUrl('*/*/import', ['ct_id' => $ctId]);
    }
}
