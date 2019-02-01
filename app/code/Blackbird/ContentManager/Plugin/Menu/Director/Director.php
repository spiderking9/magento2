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

namespace Blackbird\ContentManager\Plugin\Menu\Director;

use Blackbird\ContentManager\Api\Data\ContentTypeInterface;
use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class Director
 *
 * @package Blackbird\ContentManager\Plugin\Menu\Director
 */
class Director
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    private $_contentTypeCollectionFactory;

    /**
     * Director constructor
     *
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     */
    public function __construct(
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
    ) {
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
    }

    /**
     * Add dynamic menu of contenttypes
     *
     * @param \Magento\Backend\Model\Menu\Director\Director $subject
     * @param array $config
     * @param \Magento\Backend\Model\Menu\Builder $builder
     * @param \Psr\Log\LoggerInterface $logger
     * @return array
     */
    public function beforeDirect(
        \Magento\Backend\Model\Menu\Director\Director $subject,
        array $config,
        \Magento\Backend\Model\Menu\Builder $builder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $contentTypes = $this->_contentTypeCollectionFactory->create()->addFieldToSelect([
            ContentTypeInterface::ID,
            ContentTypeInterface::TITLE,
        ])->addFieldToFilter(ContentType::VISIBILITY, ['neq' => Visibility::REPEATER_FIELD]);

        if ($contentTypes->count()) {
            foreach ($contentTypes as $contentType) {
                $config[] = [
                    'type' => 'add',
                    'id' => 'Blackbird_ContentManager::content_' . $contentType->getId(),
                    'title' => ucwords($contentType->getTitle()),
                    'module' => 'Blackbird_ContentManager',
                    'action' => 'contentmanager/content/index/ct_id/' . $contentType->getId(),
                    'parent' => 'Blackbird_ContentManager::contents',
                    'resource' => 'Blackbird_ContentManager::contents',
                ];
            }
        } else {
            $config[] = [
                'type' => 'add',
                'id' => 'Blackbird_ContentManager::content_empty',
                'title' => __('Add a Content Type first')->render(),
                'module' => 'Blackbird_ContentManager',
                'action' => 'contentmanager/contenttype/new/',
                'parent' => 'Blackbird_ContentManager::contents',
                'resource' => 'Blackbird_ContentManager::contenttype',
            ];
        }

        return [$config, $builder, $logger];
    }
}
