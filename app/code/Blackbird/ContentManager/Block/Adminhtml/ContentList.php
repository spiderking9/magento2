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

namespace Blackbird\ContentManager\Block\Adminhtml;

/**
 * Class ContentList
 *
 * @package Blackbird\ContentManager\Block\Adminhtml
 */
class ContentList extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_contentTypeCollectionFactory;

    /**
     * ContentList constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        array $data = []
    ) {
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get create url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        $url = $this->getUrl('*/*/new');

        if ($this->hasData('create_url')) {
            $url = $this->getData('create_url');
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_contentList';
        $this->_headerText = __('Contents List');
        $this->_addButtonLabel = __('Add New Content List');

        $collection = $this->_contentTypeCollectionFactory->create();

        // If there is not content type yet
        if ($collection->getSize() === 0) {
            $this->_addButtonLabel = __('Add Content Type First');
            $this->setCreateUrl($this->getUrl('*/contenttype/new'));
        }

        parent::_construct();
    }
}
