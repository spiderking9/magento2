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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab;

/**
 * Class AbstractTab
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab
 */
abstract class AbstractTab extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Return an html sub checkbox which enable/disable the default value
     *
     * @param array $config
     * @return string
     */
    public function createRelatedCheckbox(array $config)
    {
        // Prepare config
        $config = array_merge($this->getDefaultConfig(), $config);

        $html = '<div class="control-inner-wrap">';
        $html .= '<input type="checkbox" id="' . $config['id'] . '" name="' . $config['name'] . '" value="1"';
        $html .= (!empty($config['class'])) ? ' class="' . $config['class'] . '" ' : ' ';
        $html .= ($config['use_default'] == '1') ? ' checked="checked" ' : ' ';
        $html .= ' onclick="if(this.checked){' . 'document.getElementById(\'content_' . $config['parent'] . '\').value=\'' . $this->escapeQuote($config['default'],
                true) . '\';' . 'document.getElementById(\'content_' . $config['parent'] . '\').setAttribute(\'readonly\', \'readonly\');' . '}else{' . 'document.getElementById(\'content_' . $config['parent'] . '\').value=\'' . $this->escapeQuote($config['value'],
                true) . '\';' . 'document.getElementById(\'content_' . $config['parent'] . '\').removeAttribute(\'readonly\');' . '}"';
        $html .= ' />';
        $html .= '<label for="' . $config['id'] . '">' . $config['label'] . '</label>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Retrieve default config
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        /**
         * Couple key | is required
         */
        return [
            'name' => '',
            'id' => '',
            'class' => '',
            'label' => '',
            'use_default' => '',
            'default' => '',
            'value' => '0',
            'parent' => '',     // Parent element id
        ];
    }
}
