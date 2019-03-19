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
namespace Blackbird\MenuManager\Block\Adminhtml\Menu\Edit;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;
use Blackbird\MenuManager\Controller\Adminhtml\Menu\Edit;
use Blackbird\MenuManager\Model\NodeTypeProvider;

class Nodes extends Template implements  NodeInterface
{
    protected $_template = 'menu/nodes.phtml';
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
     */
    protected $_enabledisable;

    /**
     * Nodes constructor.
     * @param Template\Context $context
     * @param NodeRepositoryInterface $nodeRepository
     * @param NodeTypeProvider $nodeTypeProvider
     * @param Registry $registry
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->_enabledisable = $enabledisable;
    }

    public function renderNodes()
    {
        $menu = $this->registry->registry(Edit::REGISTRY_CODE);
        if ($menu) {
            $nodes = $this->nodeRepository->getByMenu($menu->getId());
            $data = [];
            foreach ($nodes as $node) {
                $level = $node->getLevel();
                $parent = $node->getParentId() ?: 0;
                if (!isset($data[$level])) {
                    $data[$level] = [];
                }
                if (!isset($data[$level][$parent])) {
                    $data[$level][$parent] = [];
                }
                $data[$level][$parent][] = $node;
            }
            return $this->renderNodeList(0, null, $data);
        }
        return '';
    }

    private function renderNodeList($level, $parent, $data)
    {
        if (is_null($parent)) {
            $parent = 0;
        }
        if (empty($data[$level])) {
            return;
        }
        if (empty($data[$level][$parent])) {
            return;
        }
        $nodes = $data[$level][$parent];
        $html = '<ul>';
        foreach ($nodes as $node) {
                $html .= '<li class="jstree-open" data-type="' . $node->getType() . '" data-entity_id="' . $node->getEntityId()
                . '" data-classes="' . $node->getClasses() . '" id="node_' . $node->getId() . '" data-status="' . $node->getIsActive() .
                '"data-target="' . $node->getTarget() . '" data-url_path="' . $node->getUrlPath() . '" data-canonical="' . $node->getCanonical() .
                '" data-link_first_child="' . $node->getLinkFirstChild() . '"">';
            $html .= $node->getTitle();
            $html .= $this->renderNodeList($level + 1, $node->getId(), $data);
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}