<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @custom_field            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\NodeType;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;

class CustomField extends AbstractNodeTypeFront
{
    protected $nodes;
    protected $custom_fieldUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;

    public function __construct(
        Context $context,
        ResourceConnection $connection,
        Profiler $profiler,
        $data = []
    ) {
        $this->connection = $connection;
        $this->profiler = $profiler;
        parent::__construct($context, $data);
    }


    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $custom_fieldIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $custom_fieldIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
    }

    /**
     * define the url of the node
     *
     * @param $node
     * @return mixed
     */
    public function getUrlNode($node)
    {
        $url = $node->getUrlPath();
        $this->setData('url', $url);

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