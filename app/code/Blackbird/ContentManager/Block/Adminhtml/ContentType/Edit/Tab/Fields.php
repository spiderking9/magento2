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
 * Class Fields
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab
 */
class Fields extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/fields.phtml';

    /**
     * Fields constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Render fieldset button
     *
     * @return string
     */
    public function getAddFieldsetButtonHtml()
    {
        return $this->getChildHtml('add_fieldset');
    }

    /**
     * Render fieldset block
     *
     * @return string
     */
    public function getFieldsetBlockHtml()
    {
        return $this->getChildHtml('fieldset_block');
    }

    /**
     * Check if the param 'GET' name id exists
     *
     * @return boolean
     */
    public function idExists()
    {
        return (!empty($this->getRequest()->getParam('id')));
    }

    /**
     * Retrieve the default page title of the content type
     *
     * @return string
     */
    public function getPageTitle()
    {
        $content = $this->_coreRegistry->registry('current_contenttype');
        $title = $content ? $content->getPageTitle() : '';

        return $title;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_fieldset', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Add New Fieldset'),
            'class' => 'add',
            'id' => 'add_new_custom_fieldset',
        ]);

        $this->addChild('fieldset_block',
            'Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Fieldset');

        parent::_prepareLayout();
    }
}