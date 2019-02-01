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
namespace Blackbird\MenuManager\Model\Menu\NodeTypes;

class Config extends \Magento\Framework\Config\Data
{
    /**
     * @param \Blackbird\MenuManager\Model\Menu\NodeTypes\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Blackbird\MenuManager\Model\Menu\NodeTypes\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'menu_node_type'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of node type by name
     *
     * @param string $name
     * @return array
     */
    public function getField($name)
    {
        return $this->get($name, []);
    }

    /**
     * Get configuration of all registered node types
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }
}
