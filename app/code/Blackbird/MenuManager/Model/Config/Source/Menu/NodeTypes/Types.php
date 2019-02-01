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
namespace Blackbird\MenuManager\Model\Config\Source\Menu\NodeTypes;

class Types implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * ContentType Field Config
     *
     * @var \Blackbird\MenuManager\Model\Menu\NodeTypes\Config
     */
    protected $_nodeTypesConfig;

    /**
     * Constructor
     *
     * @param \Blackbird\MenuManager\Model\Menu\NodeTypes\Config $config
     */
    public function __construct(\Blackbird\MenuManager\Model\Menu\NodeTypes\Config $config)
    {
        $this->_nodeTypesConfig = $config;
    }

    /**
     * @return array with all data of a node Type
     */
    public function getAllDataTypesNodes()
    {
        return $this->_nodeTypesConfig->getAll();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $types = [];

      foreach($this->_nodeTypesConfig->getAll() as $nodeType)
      {
          $types[] = ['label' => __($nodeType['label']), 'value' => $nodeType['name']];
      }

        return $types;
    }

}
