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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * Class Conditions
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Edit\Tab
 */
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Can show tab or not
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
     *
     * @return boolean
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
        parent::_prepareForm();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('contentlist_');

        /** Conditions */
        $fieldset = $form->addFieldset('conditions_fieldset',
            ['legend' => __('Content Filter (leave blank to get all contents of the content type)')]);

        $field = $fieldset->addField(ContentListData::CONDITIONS, 'text', [
            'name' => ContentListData::CONDITIONS,
            'required' => true,
        ])->setRenderer($this->getLayout()->createBlock(
            \Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Conditions::class
        ));

        /** @var \Blackbird\ContentManager\Model\ContentList $contentList */
        $contentList = $this->_coreRegistry->registry('current_contentlist');
        $data = [];
        if ($contentList) {
            $contentList->rule->setConditionsSerialized($contentList->getData(ContentListData::CONDITIONS));
            $data = $contentList->getData();
            $field->setData('rule', $contentList->rule);
        }

        $this->_eventManager->dispatch(
            'adminhtml_block_contentmanager_contentlist_config_prepareform',
            ['form' => $form]
        );

        $form->setValues($data);
        $this->setForm($form);

        return $this;
    }
}
