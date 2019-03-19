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
namespace Blackbird\MenuManager\Block\Adminhtml\NodeType;

use Blackbird\MenuManager\Api\NodeTypeInterfaceAdmin;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Magento\Store\Model\StoreManagerInterface;

class Category extends Template implements NodeTypeInterfaceAdmin
{
    /**
     * @var string
     */
    protected $_template = 'menu/nodetype/category.phtml';


    /**
     * Category constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getUrlSource()
    {
        return $this->getUrl('menumanager/category_widget/chooser/form/' . 'category_id_chooser');
    }

    /**
     * get the button to open the category chooser
     *
     * @return string
     */
    public function getOpenChooserButtonHtml()
    {
        $chooser = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button',
            'open_chooser_category',
            [
                'data' => [
                    'id' => 'open_chooser',
                    'label' => __('Open Chooser'),
                    'title' => __('Open Chooser'),
                    'class' => 'button-open-chooser',
                ]
            ])->toHtml();

        return $chooser;
    }

    /**
     * get the button to apply the chooser
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        $apply = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button',
            'apply_chooser_category',
            ['data' => [
                'id' => 'apply',
                'label' => __('Apply'),
                'title' => __('Apply'),
                'class' => 'button-apply-chooser'
            ]
            ])->toHtml();
        return $apply;
    }


}