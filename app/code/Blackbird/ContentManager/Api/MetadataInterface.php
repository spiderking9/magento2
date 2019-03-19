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

namespace Blackbird\ContentManager\Api;

/**
 * Interface MetadataInterface
 * Interface for getting attributes metadata. Note that this interface should not be used directly, use its children.
 *
 * @package Blackbird\ContentManager\Api
 * @todo NEED CHECKUP
 * @api
 */
interface MetadataInterface extends \Magento\Framework\Api\MetadataServiceInterface
{
    /**
     * Retrieve all attributes filtered by form code
     *
     * @api
     * @param string $formCode
     * @return \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributes($formCode);

    /**
     * Retrieve attribute metadata.
     *
     * @api
     * @param string $attributeCode
     * @return \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata.
     *
     * @api
     * @return \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllAttributesMetadata();

    /**
     * Get custom attributes metadata for the given data interface.
     *
     * @api
     * @param string $dataInterfaceName
     * @return \Blackbird\ContentManager\Api\Data\AttributeMetadataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomAttributesMetadata($dataInterfaceName = null);
}
