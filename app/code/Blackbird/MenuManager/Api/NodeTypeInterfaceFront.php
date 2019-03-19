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


interface NodeTypeInterfaceFront
{
    /**
     * Fetch additional data required for rendering nodes.
     *
     * Should remember all nodes passed as $nodes param internally and store for use during rendering
     *
     * @param \Blackbird\MenuManager\Api\Data\NodeInterface[] $nodes
     * @return void
     */
     public function fetchData(array $nodes);

    /**
     * Renders node content.
     *
     * @param $nodeId
     * @param $level
     * @param $classes
     * @param $children
     * @return mixed
     */
     public function getHtml($nodeId, $level, $classes, $children);


    /**
     * Renders node content edition form in editor
     *
     * @return string
     */
    public function toHtml();
}