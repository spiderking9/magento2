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
namespace Blackbird\MenuManager\Controller\Adminhtml\Menu;

use Blackbird\MenuManager\Block\Adminhtml\Menu\Edit\NodesData;
use Magento\Backend\App\Action;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ResponseInterface;
use Blackbird\MenuManager\Api\MenuRepositoryInterface;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;
use Blackbird\MenuManager\Model\Menu\NodeFactory;
use Blackbird\MenuManager\Model\MenuFactory;

class Save extends Action
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
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;
    /**
     * @var FilterGroupBuilderFactory
     */
    private $filterGroupBuilderFactory;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var NodeFactory
     */
    private $nodeFactory;
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        NodeFactory $nodeFactory,
        MenuFactory $menuFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuFactory = $menuFactory;
    }


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $hasError = false;
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $menu = $this->menuRepository->getById($id);
        } else {
            $menu = $this->menuFactory->create();
        }
        $menu->setTitle($this->getRequest()->getParam('title'));
        $menu->setIdentifier($this->getRequest()->getParam('identifier'));
        $menu->setIsActive($this->getRequest()->getParam('menu_status'));
        $menu->setStoreViews($this->getRequest()->getParam('stores'));

        $menu = $this->menuRepository->save($menu);


        if (!$id) {
            $id = $menu->getId();
        }
        $nodes = $this->getRequest()->getParam('serialized_nodes');
        if (!empty($nodes)) {
            $nodes = json_decode($nodes, true);

                $filterBuilder = $this->filterBuilderFactory->create();
                $filter = $filterBuilder->setField('menu_id')->setValue($id)->setConditionType('eq')->create();

                $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
                $filterGroup = $filterGroupBuilder->addFilter($filter)->create();

                $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
                $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

                $oldNodes = $this->nodeRepository->getList($searchCriteria)->getItems();

                $existingNodes = [];
                foreach ($oldNodes as $node) {
                    $existingNodes[$node->getId()] = $node;
                }

                $nodesToDelete = [];

                foreach ($existingNodes as $nodeId => $noe) {
                    $nodesToDelete[$nodeId] = true;
                }

                $nodeMap = [];

                foreach ($nodes as $node) {
                    $nodeId = $node['id'];
                    $matches = [];
                    if (preg_match('/^node_([0-9]+)$/', $nodeId, $matches)) {
                        $nodeId = $matches[1];
                        unset($nodesToDelete[$nodeId]);
                        $nodeMap[$node['id']] = $existingNodes[$nodeId];
                    } else {
                        $nodeObject = $this->nodeFactory->create();
                        $nodeObject->setMenuId($id);
                        $nodeObject = $this->nodeRepository->save($nodeObject);
                        $nodeMap[$nodeId] = $nodeObject;
                    }
                }

                foreach (array_keys($nodesToDelete) as $nodeId) {
                    $this->nodeRepository->deleteById($nodeId);
                }

                $path = [
                    '#' => 0,
                ];
                foreach ($nodes as $node) {
                    $nodeObject = $nodeMap[$node['id']];

                    $parents = array_keys($path);
                    $parent = array_pop($parents);
                    while ($parent != $node['parent']) {
                        array_pop($path);
                        $parent = array_pop($parents);
                    }

                    $level = count($path) - 1;
                    $position = $path[$node['parent']]++;

                    if ($node['parent'] == '#') {
                        $nodeObject->setParentId(null);
                    } else {
                        $nodeObject->setParentId($nodeMap[$node['parent']]->getId());
                    }

                    $nodeObject->setType($node['data']['type']);
                    if (isset($node['data']['classes'])) {
                        $nodeObject->setClasses($node['data']['classes']);
                    }

                    if(isset($node['data']['status'])){
                        $nodeObject->setIsActive($node['data']['status']);
                    }

                    if(isset($node['data']['target'])){
                        $nodeObject->setTarget($node['data']['target']);
                    }

                    if (isset($node['data']['entity_id'])) {
                        $nodeObject->setEntityId($node['data']['entity_id']);
                    }
                    if (isset($node['data']['url_path'])) {
                        $nodeObject->setUrlPath($node['data']['url_path']);
                    }
                    if(isset($node['data']['canonical'])){
                        $nodeObject->setCanonical($node['data']['canonical']);
                    }
                    if(isset($node['data']['link_first_child'])){
                        $nodeObject->setLinkFirstChild($node['data']['link_first_child']);
                    }

                    $nodeObject->setMenuId($id);
                    $nodeObject->setTitle($node['text']);
                    $nodeObject->setLevel($level);
                    $nodeObject->setPosition($position);

                    //Dipslay error messages
                    try {
                        $this->nodeRepository->save($nodeObject);
                    }
                    catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $hasError = true;
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                        $hasError = true;
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                        $hasError = true;
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the menu: %1', $e->getMessage()));
                }
                    $path[$node['id']] = 0;
                }
        }

        //If no error occured then a success message is displayed
        if(!$hasError)
        {
            $this->messageManager->addSuccessMessage(__('Menu saved succesfully'));
        }

        if ($this->getRequest()->getParam('back')) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/edit', ['id' => $menu->getId(), '_current' => true]);
        } else {
            $redirect = $this->resultRedirectFactory->create();
            $redirect->setPath('*/*/index');
            return $redirect;
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_MenuManager::menus');
    }

}