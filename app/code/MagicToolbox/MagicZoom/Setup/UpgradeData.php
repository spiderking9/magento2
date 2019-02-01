<?php

namespace MagicToolbox\MagicZoom\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($setup->tableExists('magiczoom_config')) {
            $tableName = $setup->getTable('magiczoom_config');
            $version = $context->getVersion();

            if ($version && version_compare($version, '1.0.1') < 0) {
                $bind = ['value' => 'Yes'];
                $where = [
                    'name = ?' => 'rightClick',
                    'status = ?' => 2
                ];
                $setup->getConnection()->update($tableName, $bind, $where);
            }
        }

        $setup->endSetup();
    }
}
