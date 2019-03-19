<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @group            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\NodeType;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Blackbird\MenuManager\Api\NodeTypeInterfaceFront;

class Group extends AbstractNodeTypeFront implements NodeInterface
{
    protected $nodes;
    protected $groupUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * Group constructor.
     * @param Context $context
     * @param ResourceConnection $connection
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $connection,
        $data = []
    ) {
        $this->connection = $connection;
        parent::__construct($context, $data);
    }


    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $groupIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $groupIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
    }

    /**
     * define the url of the node
     *
     * @param $node
     * @param $childrenArray
     * @return string
     */
    public function getUrlNode($node, $childrenArray)
    {
        $url = '';
       if($childrenArray && $node->getLinkFirstChild()){
           $url = $childrenArray[0]->getUrlPath();
           $this->setUrl($url);
           $this->setLinkFirstChild(1);
       }
       return $url;
    }

    /**
     * @param $nodeId
     * @param $level
     * @param $classes
     * @param $childrenHtml
     * @param $childrenArray
     * @return string
     */
    public function getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId)
    {
        $node = $this->nodes[$nodeId];

        $node->setUrlPath($this->getUrlNode($node, $childrenArray));

        $url = $this->getData('url');
        $classes = $this->getIsActiveClass($url, $node, $classes, $childrenArray);

        return parent::getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }
}