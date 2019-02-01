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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class Import
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType
 */
class Import extends Container
{
    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        return __('Import Content Type');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_contentType';
        $this->_blockGroup = 'Blackbird_ContentManager';
        $this->_mode = 'import';

        parent::_construct();

        $this->updateButton('save', 'label', 'Import');
    }
}
