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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Import;

use Blackbird\ContentManager\Block\Adminhtml\Content\Import\Tab\Form;

/**
 * Class Tabs
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Import
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Blackbird\ContentManager\Block\Adminhtml\Content\Import\Tab\Form
     */
    protected $importForm;

    /**
     * Import Tabs constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Blackbird\ContentManager\Block\Adminhtml\Content\Import\Tab\Form $importForm
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        Form $importForm,
        array $data = []
    ) {
        $this->importForm = $importForm;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('contentmanager_content_import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Settings'));
    }
}
