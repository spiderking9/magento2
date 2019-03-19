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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit;

/**
 * Class Tabs
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Tabs constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('contentmanager_content_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Content \'%1\'', $this->_getContentType()->getTitle()));
    }

    /**
     * Retrieve Current Content Type
     *
     * @return \Blackbird\ContentManager\Model\ContentType
     */
    private function _getContentType()
    {
        return $this->_coreRegistry->registry('current_contenttype');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('general', 'contentmanager_content_edit_tab_form');

        if ($this->_getContentType()->isVisible()) {
            $this->addTab('url_section', 'contentmanager_content_edit_tab_url');
            $this->addTab('default_meta_tag_section', 'contentmanager_content_edit_tab_meta');
        }

        $this->addTab('import_section', 'contentmanager_content_edit_tab_import');

        return $this;
    }
}
