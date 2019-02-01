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
namespace Blackbird\MenuManager\Block\Adminhtml\Menu\Edit;

use Blackbird\MenuManager\Api\Data\NodeInterface;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;
use Blackbird\MenuManager\Controller\Adminhtml\Menu\Edit;
use Blackbird\MenuManager\Model\NodeTypeProvider;
use Magento\Framework\Module\Manager;

class NodesData extends Template implements NodeInterface
{
    protected $_template = 'menu/nodes_data.phtml';
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $_enabledisable;

    /**
     * @var \Blackbird\MenuManager\Model\Config\Source\Menu\NodeTypes\Types
     */
    protected $_nodeType;

    /**
     * @var \Magento\Framework\Module\Manager;
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    protected $_packageInfo;

    /**
     * NodesData constructor.
     * @param Template\Context $context
     * @param NodeRepositoryInterface $nodeRepository
     * @param NodeTypeProvider $nodeTypeProvider
     * @param Registry $registry
     * @param \Magento\Config\Model\Config\Source\Enabledisable $enabledisable
     * @param \Blackbird\MenuManager\Model\Config\Source\Menu\NodeTypes\Types
     * @param  \Magento\Framework\Module\Manager;
     * @param \Magento\Framework\Module\PackageInfo
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        \Magento\Config\Model\Config\Source\Enabledisable $enabledisable,
        \Blackbird\MenuManager\Model\Config\Source\Menu\NodeTypes\Types $nodeType,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_enabledisable = $enabledisable;
        $this->_nodeType = $nodeType;
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->_moduleManager = $moduleManager;
        $this->_packageInfo = $packageInfo;
    }

    public function getNodeForms()
    {
        return $this->nodeTypeProvider->getAdminEditForms();
    }

     /*
     * @return array
     */
    public function getStatusInfo()
    {
        return $this->_enabledisable->toOptionArray();
    }

    /**
     * return the possible targets
     *
     * @return array
     */
    public function getPossibleTargets()
    {
        $targets = [['value' => '_self','label' => 'Self: open in the same window'],['value' => '_blank', 'label' => 'Blank: open in a new window']];
        return $targets;
    }


    /**
     * return the possible types available for a node
     *
     * @return array
     */
    public function getNodesTypes()
    {
        $arrayNodesTypes = [];


    $NodeTypesData = $this->_nodeType->getAllDataTypesNodes();
        foreach($NodeTypesData as $nodeType){

             //If the node type needs a or several modules
            if($nodeType['dependencies']) {
                $isActivable = true;
                //In case a node type needs several modules
                foreach ($nodeType['dependencies'] as $dependency) {
                    $moduleName = $dependency['module_name'];
                    $moduleVersion = $dependency['version'];

                    if ($this->_moduleManager->isEnabled($moduleName)) {
                        if (version_compare($this->_packageInfo->getVersion($moduleName), $moduleVersion, '>=')) {
                           //nothing, isActivable stay as it should be
                        } else {
                            $isActivable = false;
                        }
                    } else {
                        $isActivable = false;
                    }
                }
                //If every module dependency of a node type is correct
                if($isActivable){
                    $arrayNodesTypes[] = ['label' => $nodeType['label'], 'value' => $nodeType['name']];
                }
            } else {
                //if the node type doesn't need any module
                $arrayNodesTypes[] = ['label' => $nodeType['label'], 'value' => $nodeType['name']];
            }

        }

    return $arrayNodesTypes;
    }
}