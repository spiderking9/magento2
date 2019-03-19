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

namespace Blackbird\ContentManager\Helper;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class UrlRewriteGenerator
 *
 * @package Blackbird\ContentManager\Helper
 * @todo move the class
 */
class UrlRewriteGenerator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory
     */
    protected $_urlRewriteFactory;

    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    protected $_urlPersist;

    /**
     * UrlRewriteGenerator constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory
     * @param \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory,
        \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist
    ) {
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_urlPersist = $urlPersist;
        parent::__construct($context);
    }

    /**
     * Create and save a new UrlRewrite
     *
     * @param int $entityType
     * @param int $entityId
     * @param string $requestPath
     * @param string $targetPath
     * @param int $storeId
     * @param array $data
     * @throws \Exception
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    public function addUrlRewrite($entityType, $entityId, $requestPath, $targetPath, $storeId, $data = [])
    {
        $this->_urlPersist->replace([
            $this->createUrlRewrite($entityType, $entityId, $requestPath, $targetPath, $storeId, $data),
        ]);
    }

    /**
     * Create and return an UrlRewrite data object
     *
     * @param int $entityType
     * @param int $entityId
     * @param string $requestPath
     * @param string $targetPath
     * @param int $storeId
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    public function createUrlRewrite($entityType, $entityId, $requestPath, $targetPath, $storeId, $data = [])
    {
        return $this->_urlRewriteFactory->create($data)
            ->setEntityType($entityType)
            ->setEntityId($entityId)
            ->setRequestPath($requestPath)
            ->setTargetPath($targetPath)
            ->setStoreId($storeId);
    }

    /**
     * Create and save many UrlRewrite
     *
     * @param array $data
     * @throws \Exception
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    public function addUrlRewrites(array $data)
    {
        $urls = [];

        foreach ($data as $values) {
            $data = !empty($values['data']) && is_array($values['data']) ?: [];

            $urls[] = $this->createUrlRewrite($values['entity_type'], $values['entity_id'], $values['request_path'],
                $values['target_path'], $values['store_id'], $data);
        }

        $this->_urlPersist->replace($urls);
    }

    /**
     * Delete the UrlRewrite
     *
     * @param array|string $entityType
     * @param array|int $entityId
     * @param int $storeId
     */
    public function deleteUrlRewrite($entityType, $entityId = null, $storeId = null)
    {
        $data = [UrlRewrite::ENTITY_TYPE => $entityType];

        if ($entityId !== null) {
            $data[UrlRewrite::ENTITY_ID] = $entityId;
        }
        if ($storeId !== null) {
            $data[UrlRewrite::STORE_ID] = $storeId;
        }

        $this->_urlPersist->deleteByData($data);
    }
}
