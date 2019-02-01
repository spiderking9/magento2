<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @product            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\NodeType;

use Braintree\Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class Product extends AbstractNodeTypeFront
{
    protected $nodes;

    protected $productUrls;

    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProductHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepositoryInterface;

    /**
     * Product constructor.
     * @param Context $context
     * @param ResourceConnection $connection
     * @param \Magento\Catalog\Helper\Product $catalogProductHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $connection,
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        $data = []
    ) {
        $this->connection = $connection;
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        parent::__construct($context, $data);
    }


    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $productIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $productIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
    }

    /**
     * define the url of the node
     *
     * @param $node
     * @return bool|string
     */
    public function getUrlNode($node)
    {
        //construct the url of the product attached to the node if the sku exist
        try {
            $productId = $this->_productRepositoryInterface->get($node->getEntityId())->getId();
            $url = $this->_catalogProductHelper->getProductUrl($productId);
        } catch (NoSuchEntityException $exception) {
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