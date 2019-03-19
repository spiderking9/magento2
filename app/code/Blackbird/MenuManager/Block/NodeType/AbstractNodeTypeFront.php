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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Template;

abstract class AbstractNodeTypeFront extends Template
{
    /**
     * @var
     */
   protected $nodes;

   public function __construct(Context $context, $data = [])
   {
       parent::__construct($context, $data);
   }

    public function getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId)
    {

        $node = $this->nodes[$nodeId];

        $this->setStatus($node->getData('is_active'));
        $this->setTitle($node->getTitle());
        $this->setClasses($classes);
        $this->setLevel($level);
        $this->setTarget($node->getTarget());
        $this->setChildren($childrenHtml);

       if(!$this->getData('url')){
           $this->setData('url', '/');
       }

        $this->setData('entity_id', $node->getEntityId());

        return $this->toHtml();
    }

    /*
     * @param $urlNodeItem
     * @param $node
     * @param $children
     * @return bool
     */
    public function isActive($urlNodeItem, $node, $childrenArray)
    {
        $url1 = parse_url($urlNodeItem);
        $url2 = parse_url($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]));

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');

        if((!isset($url1['host']) || !$url1['host'] || !isset($url2['host']) || !$url2['host'] || $url1['host'] == $url2['host']) && (isset($url1['path']) && isset($url2['path']) && rtrim($url1['path'], '/') == rtrim($url2['path'], '/')))
        {
            return true;
        }
        else if($node->getType() == 'category' && $category && $category->getId() == $node->getEntityId())
        {
            return true;
        }

        if($childrenArray !=null)
        {
            foreach($childrenArray as $child)
            {
                if($child->getActiveClass())
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $url
     * @param $node
     * @param $classes
     * @param $children
     * @return array
     */
    public function getIsActiveClass($url, $node, $classes, $childrenArray)
    {

        if($this->isActive($url, $node, $childrenArray)) {
            $classes[] = 'menu-item-active';
            //used to know if a children of the current node is Active
            $node->setData('active_class', true);
        }

        return $classes;
    }

    /**
     * method to prepare the template
     *
     * @param $type
     * @param $menuIdentifier
     * @return $this
     */
    public function prepareTemplate($type, $menuIdentifier)
    {
        //Custom template if existing
        //$menuidentifier is the identifier of the menu used for every pages defined in the default.xml
        $this->setTemplate('Blackbird_MenuManager::menu/view/' . $menuIdentifier . '/nodetype/'. $type .'.phtml');

        if(!$this->getTemplateFile()) {
            //default case
            $this->setTemplate('Blackbird_MenuManager::menu/view/default/nodetype/'. $type .'.phtml');
        }

        return $this;
    }





}