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

/**
 * Content edit form block
 *
 * Class Edit
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Default block template
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit.phtml';

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
     * Get Save Split Button html
     *
     * @return string
     */
    public function getSaveSplitButtonHtml()
    {
        return $this->getChildHtml('save-split-button');
    }

    /**
     * Get save and continue button Url
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('contentmanager/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}', 'active_tab' => null]);
    }

    /**
     * Get duplicate button Url
     *
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->getUrl('contentmanager/*/duplicate', ['_current' => true]);
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $contentTypeId = $this->isContentNew()
            ? $this->getRequest()->getParam('ct_id')
            : $this->getContent()
                ->getCtId();

        return $this->getUrl('contentmanager/*/', ['ct_id' => $contentTypeId]);
    }

    /**
     * Check whether new content is being created
     *
     * @return bool
     */
    public function isContentNew()
    {
        $content = $this->getContent();

        return (!$content || !$content->getId());
    }

    /**
     * Retrieve currently edited content object
     *
     * @return \Blackbird\ContentManager\Model\Content
     */
    public function getContent()
    {
        return $this->_coreRegistry->registry('current_content');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_content';
        $this->_blockGroup = 'Blackbird_ContentManager';

        if (!$this->isContentNew()) {
            $this->addButton('delete_translation', [
                'label' => __('Delete Translation'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\'Are you sure you want to delete this translation ?\', \'' . $this->getUrl('contentmanager/*/delete',
                        [
                            'id' => $this->getContentId(),
                            'store' => $this->getRequest()->getParam('store', 0),
                        ]) . '\')',
            ], 0, 30);

            $this->addButton('preview', [
                'class' => 'action-secondary',
                'label' => __('Preview'),
                'onclick' => 'window.open(\'' . $this->getPreviewUrl() . '\', \'_blank\')',
            ]);
        }

        parent::_construct();

        $this->removeButton('save');
    }

    /**
     * Retrieve content ID
     *
     * @return int
     */
    public function getContentId()
    {
        return $this->getContent()->getId();
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        $url = '';
        if (!$this->isContentNew()) {
            $url = $this->getContent()->getLinkUrl($this->getContent()->getStore()->getCode(), true);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild('save-split-button', 'Magento\Backend\Block\Widget\Button\SplitButton', [
            'id' => 'save-split-button',
            'label' => __('Save'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                ],
            ],
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'button_class' => 'widget-button-save',
            'options' => $this->_getSaveSplitButtonOptions(),
        ]);

        return parent::_prepareLayout();
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {
        $options = [];

        $options[] = [
            'id' => 'new-button',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndNew', 'target' => '#edit_form'],
                ],
            ],
        ];

        $options[] = [
            'id' => 'duplicate-button',
            'label' => __('Save & Duplicate'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndDuplicate', 'target' => '#edit_form'],
                ],
            ],
        ];

        $options[] = [
            'id' => 'close-button',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndClose', 'target' => '#edit_form']],
            ],
        ];

        return $options;
    }
}
