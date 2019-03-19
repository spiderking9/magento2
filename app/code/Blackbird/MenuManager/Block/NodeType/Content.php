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
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;

class Content extends AbstractNodeTypeFront implements NodeInterface
{
    protected $nodes;
    protected $contentUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;


    /**
     * @var \Blackbird\ContentManager\Model\ContentFactory $_contentFactory
     */
    protected $_contentFactory;

    /**
     * Content constructor.
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

    protected function getContentFactory()
    {
        if(!$this->_contentFactory){
            $this->_contentFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Blackbird\ContentManager\Model\ContentFactory::class);
        }
        return $this->_contentFactory;
    }


    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $contentIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $contentIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
    }

    /**
     * define the url of the node
     *
     * @param $node
     * @return string
     */
    public function getUrlNode($node, $storeId)
    {
        if($node->getCanonical()) {
            $content = $this->getContentFactory()->create()->setStoreId($storeId)->load($node->getEntityId());
            $url = $content->getLinkUrl();
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

        $node->setUrlPath($this->getUrlNode($node, $storeId));

        $url = $this->getData('url');
        $classes = $this->getIsActiveClass($url, $node, $classes, $childrenArray);

        return parent::getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }
}