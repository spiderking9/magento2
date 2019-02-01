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

namespace Blackbird\ContentManager\Model\ContentType\Layout;

use Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Group as ResourceGroup;

/**
 * Class Group
 *
 * @package Blackbird\ContentManager\Model\ContentType\Layout
 */
class Group extends \Blackbird\ContentManager\Model\ContentType\Layout\AbstractGroup
    implements \Blackbird\ContentManager\Api\Data\ContentType\Layout\GroupInterface
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory
     */
    protected $_layoutBlockCollectionFactory;

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory
     */
    protected $_layoutFieldCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory $layoutBlockCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory $layoutFieldCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Block\CollectionFactory $layoutBlockCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\Layout\Field\CollectionFactory $layoutFieldCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_layoutBlockCollectionFactory = $layoutBlockCollectionFactory;
        $this->_layoutFieldCollectionFactory = $layoutFieldCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceGroup::class);
        $this->setIdFieldName(self::ID);
        $this->setType('group');
    }
}
