<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\Adminhtml\Menu\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Blackbird\MenuManager\Controller\Adminhtml\Menu\Edit;

class Main extends Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enabledisabled;


    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data)
    {
        $this->_systemStore = $systemStore;
        $this->_enabledisabled = $enabledisable;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('menu_');

        $menu = $this->_coreRegistry->registry(Edit::REGISTRY_CODE);
        if ($menu) {
            $form->addField('menu_id', 'hidden', ['name' => 'id']);
        }

        $fieldSet = $form->addFieldset(
            'menu_fieldset',
            ['legend' => __('Menu Data'), 'class' => 'fieldset-wide', 'collapsable' => true]
        );

        $fieldSet->addField(
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Title'),
                'class' => 'required',
                'required' => true,
            ]
        );

        $fieldSet->addField(
            'identifier',
            'text',
            [
                'name'  => 'identifier',
                'label' => __('Identifier'),
                'class' => 'required',
                'required' => true,
            ]
        );

        $fieldSet->addField(
            'menu_status',
            'select',
            [
                'name' => 'menu_status',
                'label' => 'Status',
                'class' => 'required',
                 'required' => true,
                'values' => $this->_enabledisabled->toOptionArray(),
            ]
        );

        $fieldSet->addField(
            'stores',
            'multiselect',
            [
                'name' => 'stores',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            ]
        );

        $this->setForm($form);
    }

    protected function _initFormValues()
    {
        $menu = $this->_coreRegistry->registry(Edit::REGISTRY_CODE);
        if ($menu) {
            $this->getForm()->setValues($menu->getData());
        }
    }
}