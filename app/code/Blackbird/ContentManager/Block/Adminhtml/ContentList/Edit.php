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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList;

/**
 * Content list edit form block
 *
 * Class Edit
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Edit constructor
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
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_contentList';
        $this->_blockGroup = 'Blackbird_ContentManager';

        parent::_construct();

        if (!$this->isContentListNew()) {
            $this->addButton('preview', [
                'class' => 'preview',
                'label' => __('Preview'),
                'onclick' => 'window.open(\'' . $this->getPreviewUrl() . '\', \'_blank\')',
            ]);
        }

        $this->addButton('save_and_continue_edit', [
            'class' => 'save',
            'label' => __('Save and Continue Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'saveAndContinueEdit',
                        'target' => '#edit_form',
                    ],
                ],
            ],
        ], 10);

        $this->updateButton('save', 'label', 'Save Content List');
        $this->updateButton('delete', 'label', 'Delete Content List');
    }

    /**
     * Check whether new content list is being created
     *
     * @return bool
     */
    public function isContentListNew()
    {
        $contentList = $this->getContentList();

        return (!$contentList || !$contentList->getId());
    }

    /**
     * Retrieve currently edited content list object
     *
     * @return \Blackbird\ContentManager\Model\ContentList
     */
    public function getContentList()
    {
        return $this->_coreRegistry->registry('current_contentlist');
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        $url = '';
        if (!$this->isContentListNew()) {
            $query = [
                '___store' => $this->_storeManager->getDefaultStoreView()->getCode(),
                'preview' => 1,
            ];
            $url = $this->getUrl('', ['_direct' => $this->getContentList()->getUrlKey(), '_query' => $query]);
        }

        return $url;
    }
}
