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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;

class CmsBlock extends AbstractNodeTypeFront implements  NodeInterface
{
    protected $nodes;
    protected $contentUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $_cmsBlockRepositoryInterface;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * CmsBlock constructor.
     * @param Context $context
     * @param ResourceConnection $connection
     * @param \Magento\Cms\Api\BlockRepositoryInterface $cmsBlockRepositoryInterface
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $connection,
        \Magento\Cms\Api\BlockRepositoryInterface $cmsBlockRepositoryInterface,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        $data = []
    )
    {
        $this->connection = $connection;
        $this->_cmsBlockRepositoryInterface = $cmsBlockRepositoryInterface;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
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

        try {
            //Actually get the the cms block by its identifier
            $cmsBlock = $this->_cmsBlockRepositoryInterface->getById($node->getEntityId());
            $this->setBlockContent($cmsBlock->getContent());
        }
        catch(NoSuchEntityException $e) {
            //do nothing
        }

        return parent::getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }


    /**
     * get processed value
     *
     * @param $value
     * @return mixed
     */
    public function getProcessedData($value)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_filterProvider->getBlockFilter()
            ->setStoreId($storeId)
            ->filter($value);
    }
}