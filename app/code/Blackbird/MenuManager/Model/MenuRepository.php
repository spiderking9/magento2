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
namespace Blackbird\MenuManager\Model;

use Blackbird\MenuManager\Api\MenuRepositoryInterface;
use Blackbird\MenuManager\Api\Data\MenuInterface;
use Blackbird\MenuManager\Model\ResourceModel\Menu\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Store\Model\Store;

class MenuRepository implements MenuRepositoryInterface
{
    const ID_ALL_STORE_VIEW = 0;

    protected $objectFactory;
    protected $collectionFactory;

    /**
     * @var \Blackbird\MenuManager\Model\MenuFactory
     */
    protected $_menuFactory;

    /**
     * @var \Blackbird\MenuManager\Model\ResourceModel\Menu
     */
    protected $_menuResourceModel;

    public function __construct(
        MenuFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        \Blackbird\MenuManager\Model\MenuFactory $menuFactory,
        \Blackbird\MenuManager\Model\ResourceModel\Menu $menuResourceModel
    ) {
        $this->objectFactory = $objectFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->_menuFactory = $menuFactory;
        $this->_menuResourceModel = $menuResourceModel;
    }

    public function save(MenuInterface $object)
    {

        try {
            $object->save();

        } catch (Exception $e) {

            throw new CouldNotSaveException($e->getMessage());
        }

        return $object;
    }

    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;
    }

    public function delete(MenuInterface $object)
    {
        try {
            $object->delete();
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;
    }

    public function get($identifier, $storeId)
    {
        $menu = null;

        $storesAvailable = $this->_menuResourceModel->lookupStoreIdsByIdentifier($identifier);

        if((!is_array($storesAvailable) || !in_array($storeId, $storesAvailable) && !in_array(Store::DEFAULT_STORE_ID, $storesAvailable))) {
            throw new NoSuchEntityException(__("This entity doesn't exist for this store ID"));
        } else {
            $menu = $this->collectionFactory->create()
                ->addStoreFilter($identifier, $storeId)
                ->getFirstItem();

            if($menu->getId() == null && in_array(0, $storesAvailable))
            {
                $menu = $this->collectionFactory->create()
                    ->addStoreFilter($identifier, 0)
                    ->getFirstItem();
            }
        }

        return $menu;
    }
}
