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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class Conditions
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget
 */
class Conditions extends Template implements RendererInterface
{
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @var \Blackbird\ContentManager\Model\Rule
     */
    protected $_rule;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var AbstractElement
     */
    protected $_element;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $_input;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/widget/conditions.phtml';

    /**
     * Conditions constructor
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Blackbird\ContentManager\Model\RuleFactory $rule
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Blackbird\ContentManager\Model\RuleFactory $rule,
        \Magento\Framework\Registry $registry,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_conditions = $conditions;
        $this->_rule = $rule->create();
        $this->_coreRegistry = $registry;
        $this->conditionsHelper = $conditionsHelper;
        $this->_widgetFactory = $widgetFactory;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        if ($element->getData('value')) {
            $arr = $this->getConditionsDecoded($element->getData('value'));
            if ($arr) {
                $this->_rule->getConditions()->setConditions([])->loadArray($arr);
            }
        }

        return $this->toHtml();
    }

    /**
     * Retrieve new child URL
     *
     * @return string
     */
    public function getNewChildUrl()
    {
        return $this->getUrl(
            'contentmanager/content_widget/conditions',
            ['form' => $this->getElement()->getContainer()->getHtmlId()]
        );
    }

    /**
     * Get Element object
     *
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Get Element Id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getElement()->getContainer()->getHtmlId();
    }

    /**
     * Get input html rendering
     *
     * @return string
     * @todo render conditions by Custom Field type
     */
    public function getInputHtml()
    {
        $this->_input = $this->_elementFactory->create('text');
        $this->_input->setRule($this->_rule)->setValues()->setRenderer($this->_conditions);

        return $this->_input->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $widget = $this->getRequest()->getParam('widget');
        $instanceId = $this->getRequest()->getParam('instance_id');

        // Widget for wysiwyg
        if ($widget) {
            $widgetParams = json_decode($widget);

            if ($widgetParams && isset($widgetParams->values, $widgetParams->values->conditions_encoded)) {
                $this->setData('load_post_conditions', $this->getConditionsDecoded($widgetParams->values->conditions_encoded));
            }
            // Widget for Blocks
        } elseif ($instanceId) {
            $widgetInstance = $this->_widgetFactory->create()->load($instanceId);
            $widgetParam = $widgetInstance->getWidgetParameters();

            if (!empty($widgetParam['conditions'])) {
                $this->setData('load_post_conditions', $widgetParam['conditions']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        if ($this->getData('load_post_conditions')) {
            $this->_rule->loadPost(['conditions' => $this->getData('load_post_conditions')]);
        }

        return parent::_beforeToHtml();
    }

    /**
     * Get rule conditions
     *
     * @param string $conditionsEncoded
     * @return array
     */
    protected function getConditionsDecoded($conditionsEncoded)
    {
        return $this->conditionsHelper->decode($conditionsEncoded);
    }
}
