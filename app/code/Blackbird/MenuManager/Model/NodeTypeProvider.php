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

class NodeTypeProvider
{
    /**
     * @var array
     */
    protected $providers_admin;

    /**
     * @var array
     */
    protected $providers_front;

    /**
     * @var Config\Source\Menu\NodeTypes\Types
     */
    protected $types;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * NodeTypeProvider constructor.
     * @param Config\Source\Menu\NodeTypes\Types $types
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     */
    public function __construct(
        \Blackbird\MenuManager\Model\Config\Source\Menu\NodeTypes\Types $types,
        \Magento\Framework\View\Element\BlockFactory $blockFactory
    )
    {
        $this->types = $types;
        $this->blockFactory = $blockFactory;

        $providers_front = $this->getProviders('front');
        $providers_admin = $this->getProviders('admin');

        $this->providers_front = $providers_front;
        $this->providers_admin = $providers_admin;
    }

    /**
     * @param string $area
     * @return array
     */
    protected function getProviders($area = 'front')
    {
        $providers = array();

        foreach($this->types->getAllDataTypesNodes() as $nodeTypeKey => $nodeTypeConfig)
        {
            $providers[$nodeTypeKey] = $this->blockFactory->createBlock($nodeTypeConfig['renderer_'.$area]);
        }

        return $providers;
    }

    /**
     * @param $type
     * @param $nodes
     */
    public function prepareFrontData($type, $nodes)
    {
        $this->providers_front[$type]->fetchData($nodes);
    }

    /**
     * @param $type
     * @param $id
     * @param $level
     * @return mixed
     */
    public function renderFront($menuIdentifier, $type, $id, $level, $classes, $childrenHtml, $childrenArray, $storeId)
    {
        return $this->providers_front[$type]->prepareTemplate($type, $menuIdentifier)->getHtml($id, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }

    public function getAdminEditForms()
    {
        return $this->providers_admin;
    }
}