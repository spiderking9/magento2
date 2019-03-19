<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_ContentManager
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Blackbird\ContentManager\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Content setup factory
     *
     * @var ContentSetupFactory
     */
    private $_contentSetupFactory;

    /**
     * InstallData constructor
     *
     * @param ContentSetupFactory $contentSetupFactory
     */
    public function __construct(
        ContentSetupFactory $contentSetupFactory
    ) {
        $this->_contentSetupFactory = $contentSetupFactory;
    }

    /**
     * Installs entities for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var ContentSetup $contentSetup */
        $contentSetup = $this->_contentSetupFactory->create(['setup' => $setup]);

        // Install defaults entities
        $contentSetup->installEntities();
    }
}
