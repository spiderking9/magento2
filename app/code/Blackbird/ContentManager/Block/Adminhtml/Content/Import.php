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

namespace Blackbird\ContentManager\Block\Adminhtml\Content;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Import
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content
 */
class Import extends Container
{
    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        return __('Import Content');
    }

    /**
     * {@inheritdoc}
     */
    public function getBackUrl()
    {
        return $this->getUrl('contentmanager/*/', ['ct_id' => $this->getRequest()->getParam('ct_id')]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_content';
        $this->_blockGroup = 'Blackbird_ContentManager';
        $this->_mode = 'import';

        parent::_construct();

        $this->updateButton('save', 'label', 'Import');
        $this->updateButton('save', 'id', 'import_button');

    }
}
