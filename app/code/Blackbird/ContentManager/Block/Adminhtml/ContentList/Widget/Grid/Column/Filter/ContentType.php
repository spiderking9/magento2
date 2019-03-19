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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Filter;

/**
 * Class ContentType
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Filter
 */
class ContentType extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentTypes
     */
    protected $_contentTypeSource;

    /**
     * ContentType constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypeSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypeSource,
        array $data = []
    ) {
        $this->_contentTypeSource = $contentTypeSource;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * Get condition
     *
     * @return array|null
     */
    public function getCondition()
    {
        if ($this->getValue() === null) {
            return null;
        }

        return ['eq' => $this->getValue()];
    }

    /**
     * Get options
     *
     * @return array
     */
    protected function _getOptions()
    {
        if (!$this->hasData('options')) {
            $array = [];

            foreach ($this->_contentTypeSource->getOptions() as $key => $value) {
                $array[] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }

            $this->setData('options', $array);
        }

        return $this->getData('options');
    }
}
