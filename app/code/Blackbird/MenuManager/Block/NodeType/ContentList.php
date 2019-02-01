<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @content            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\NodeType;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;

class ContentList extends AbstractNodeTypeFront implements NodeInterface
{
    protected $nodes;
    protected $contentListUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var \Blackbird\ContentManager\Model\ContentListFactory $_contentListFactory
     */
    protected $_contentListFactory;

    /**
     * ContentList constructor.
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

    protected function getContentListFactory()
    {
        if(!$this->_contentListFactory)
        {
            $this->_contentListFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Blackbird\ContentManager\Model\ContentListFactory::class);
        }
        return $this->_contentListFactory;
    }

    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $contentListIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $contentListIds[] = (int)$node->getContent();
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
        if($node->getCanonical()) {
            $contentList = $this->getContentListFactory()->create()->load($node->getEntityId());
            $url = $this->getUrl($contentList->getUrl());
        } else{
            $url = $node->getUrlPath();
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