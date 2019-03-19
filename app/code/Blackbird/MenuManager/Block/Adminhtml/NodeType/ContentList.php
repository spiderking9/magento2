<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @content            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */

namespace Blackbird\MenuManager\Block\Adminhtml\NodeType;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Blackbird\MenuManager\Api\NodeTypeInterfaceAdmin;

class ContentList extends Template implements NodeTypeInterfaceAdmin, NodeInterface
{
    /**
     * @var string
     */
    protected $_template = 'menu/nodetype/contentlist.phtml';

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
        return $this->getUrl('menumanager/contentlist_widget/chooser/form/' . 'contentlist_id');
    }

    /**
     * @return array values and label for yes or no choices
     */
    public function getIsCanonical()
    {
        return self::SELECT_YESNO;
    }

    /**
     * get the button to open the category chooser
     *
     * @return string
     */
    public function getOpenChooserButtonHtml()
    {
        $chooser = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button',
            'open_chooser_contentlist',
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
            'apply_chooser_contentlist',
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