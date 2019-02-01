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
namespace Blackbird\MenuManager\Block\NodeType;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Blackbird\MenuManager\Api\NodeTypeInterfaceFront;

class CmsPage extends AbstractNodeTypeFront
{
    protected $nodes;
    protected $pageUrls;
    protected $pageIds;
    /**
     * @var ResourceConnection
     */
    private $connection;


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
        $pagesCodes = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $pagesCodes[] = $node->getContent();
        }
        $this->nodes = $localNodes;
    }

    /**
     * define the url of the node
     *
     * @param $node
     * @return string
     */
    public function getUrlNode($node)
    {
        if($node->getEntityId()){
            $url = $this->_storeManager->getStore()->getBaseUrl() . $node->getEntityId();
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl();
        }

        $this->setUrl($url);
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

        $node->setUrlPath($this->getUrlNode($node));

        $url = $this->getData('url');
        $classes = $this->getIsActiveClass($url, $node, $classes, $childrenArray);

        return parent::getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }
}