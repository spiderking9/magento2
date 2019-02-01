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

namespace Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Renderer;

use Blackbird\ContentManager\Api\Data\ContentListInterface as ContentListData;

/**
 * ContentType grid column
 *
 * Class ContentType
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentList\Widget\Grid\Column\Renderer
 */
class ContentType extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentTypes
     */
    protected $_contentTypeSource;

    /**
     * ContentType constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypeSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Blackbird\ContentManager\Model\Config\Source\ContentTypes $contentTypeSource,
        array $data = []
    ) {
        $this->_contentTypeSource = $contentTypeSource;
        parent::__construct($context, $data);
    }

    /**
     * Render grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $str = '';
        $contentTypes = $this->_contentTypeSource->getOptions();

        if (isset($contentTypes[$row->getData(ContentListData::CT_ID)])) {
            $str = $contentTypes[$row->getData(ContentListData::CT_ID)];
        }

        return $str;
    }
}
