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
namespace Blackbird\MenuManager\Block;

use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Blackbird\MenuManager\Api\MenuRepositoryInterface;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;
use Blackbird\MenuManager\Model\NodeTypeProvider;
use Magento\Store\Model\Store;

class Menu extends Template implements IdentityInterface
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    private $nodes;
    private $menu;
    /**
     * @var SearchCriteriaFactory
     */
    private $searchCriteriaFactory;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    public function __construct(
        Template\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        SearchCriteriaFactory $searchCriteriaFactory,
        FilterGroupBuilder $filterGroupBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->setData('cache_lifetime', false); // infinite caching
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        if (!$this->menu) {
            $this->fetchData();
        }
        if($this->menu) {
            return [\Blackbird\MenuManager\Model\Menu::CACHE_TAG . '_' . $this->menu->getId()];
        }
        return [];
    }

    public function getCacheKeyInfo()
    {
        if (!$this->menu) {
            $this->fetchData();
        }
        if($this->menu) {
            return [
                \Blackbird\MenuManager\Model\Menu::CACHE_TAG,
                'menu_' . $this->menu->getId(),
                'store_' . $this->_storeManager->getStore()->getId(),
            ];
        }
        return [];
    }

    public function getMenuHtml($level = 0, $parent = null)
    {
        $nodes = $this->getNodes($level, $parent);

        $html = '';
        $i = 0;
        foreach ($nodes as $node) {

            $childrenHtml = $this->getMenuHtml($level + 1, $node);

            $classes = [
                'level' . $level,
                $node->getClasses() ?: '',
            ];

            if ($i == 0) {
                $classes[] = 'first';
            }
            if ($i == count($nodes) - 1) {
                $classes[] = 'last';
            }
            if ($level == 0) {
                $classes[] = 'level-top';
            }

            $childrenArray = $this->getNodes($level + 1, $node);

            $html .= $this->renderNode($node, $level, $classes, $childrenHtml, $childrenArray);

            ++$i;

        }

        return $html;
    }

    private function getNodes($level, $parent)
    {
        if (empty($this->nodes)) {
            $this->fetchData();
        }
        if (!isset($this->nodes[$level])) {
            return [];
        }
        $parentId = $parent['node_id'] ?: 0;
        if (!isset($this->nodes[$level][$parentId])) {
            return [];
        }

        return $this->nodes[$level][$parentId];
    }

    private function fetchData()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->menu = $this->menuRepository->get($this->getData('menu'), $storeId);

            $nodes = $this->nodeRepository->getByMenu($this->menu->getId());
            $result = [];
            $types = [];
            foreach ($nodes as $node) {
                $level = $node->getLevel();
                $parent = $node->getParentId() ?: 0;
                if (!isset($result[$level])) {
                    $result[$level] = [];
                }
                if (!isset($result[$level][$parent])) {
                    $result[$level][$parent] = [];
                }
                $result[$level][$parent][] = $node;
                $type = $node->getType();
                if (!isset($types[$type])) {
                    $types[$type] = [];
                }
                $types[$type][] = $node;
            }
            $this->nodes = $result;

            foreach ($types as $type => $nodes) {
                $this->nodeTypeProvider->prepareFrontData($type, $nodes);
            }
        } catch (NoSuchEntityException $e) {

        }
    }

    public function _prepareLayout()
    {
        $this->setTemplate('Blackbird_MenuManager::menu/view/' . $this->getData('menu') . '/menu.phtml');

        if(!$this->getTemplateFile()) {
            //default case
            $this->setTemplate('Blackbird_MenuManager::menu/view/default/menu.phtml');
        }

        return parent::_prepareLayout();
    }

    private function renderNode($node, $level, $classes, $childrenHtml, $childrenArray)
    {
        $type = $node->getType();
        $storeId = $this->menu->getStoreId();
        return $this->nodeTypeProvider->renderFront($this->menu->getIdentifier(), $type, $node->getId(), $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }

}