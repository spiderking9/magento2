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
namespace Blackbird\MenuManager\Api;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NodeRepositoryInterface
{
    public function save(NodeInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(NodeInterface $page);

    public function deleteById($id);

    public function getByMenu($menuId);
}
