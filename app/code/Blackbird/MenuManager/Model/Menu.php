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
namespace Blackbird\MenuManager\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Blackbird\MenuManager\Api\Data\MenuInterface;
use Magento\Store\Model\StoreManagerInterface;


class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{
    const CACHE_TAG = 'blackbird_menumanager_menu';

    protected $_cacheTag = self::CACHE_TAG;

    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected function _construct()
    {
        $this->_init('Blackbird\MenuManager\Model\ResourceModel\Menu');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Return the stores
     *
     * @return Store[]
     */
    public function getStores()
    {
        $stores = $this->getStoreViews();

        return $stores;
    }

}
