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

namespace Blackbird\ContentManager\Api\Data;

/**
 * Interface AttributeMetadataInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 */
interface AttributeMetadataInterface extends \Magento\Framework\Api\MetadataObjectInterface
{
    const ATTRIBUTE_CODE = 'attribute_code';
    const FRONTEND_INPUT = 'frontend_input';
    const STORE_LABEL = 'store_label';
    const OPTIONS = 'options';
    const VISIBLE = 'visible';
    const REQUIRED = 'required';
    const USER_DEFINED = 'user_defined';
    const FRONTEND_CLASS = 'frontend_class';
    const FRONTEND_LABEL = 'frontend_label';
    const SYSTEM = 'system';
    const NOTE = 'note';
    const BACKEND_TYPE = 'backend_type';
    const IS_SEARCHABLE = 'is_searchable';
    const SEARCH_WEIGHT = 'search_weight';

    /**
     * Frontend HTML for input element.
     *
     * @api
     * @return string
     */
    public function getFrontendInput();

    /**
     * Set frontend HTML for input element.
     *
     * @api
     * @param string $frontendInput
     * @return $this
     */
    public function setFrontendInput($frontendInput);

    /**
     * Get label of the store.
     *
     * @api
     * @return string
     */
    public function getStoreLabel();

    /**
     * Set label of the store.
     *
     * @api
     * @param string $storeLabel
     * @return $this
     */
    public function setStoreLabel($storeLabel);

    /**
     * Whether attribute is visible on frontend.
     *
     * @api
     * @return bool
     */
    public function isVisible();

    /**
     * Set whether attribute is visible on frontend.
     *
     * @api
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible);

    /**
     * Whether attribute is required.
     *
     * @api
     * @return bool
     */
    public function isRequired();

    /**
     * Set whether attribute is required.
     *
     * @api
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired);

    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @api
     * @return \Blackbird\ContentManager\Api\Data\OptionInterface[]
     */
    public function getOptions();

    /**
     * Set options of the attribute (key => value pairs for select)
     *
     * @api
     * @param \Blackbird\ContentManager\Api\Data\OptionInterface[] $options
     * @return $this
     */
    public function setOptions(array $options = null);

    /**
     * Get class which is used to display the attribute on frontend.
     *
     * @api
     * @return string
     */
    public function getFrontendClass();

    /**
     * Set class which is used to display the attribute on frontend.
     *
     * @api
     * @param string $frontendClass
     * @return $this
     */
    public function setFrontendClass($frontendClass);

    /**
     * Whether current attribute has been defined by a user.
     *
     * @api
     * @return bool
     */
    public function isUserDefined();

    /**
     * Set whether current attribute has been defined by a user.
     *
     * @api
     * @param bool $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined);

    /**
     * Get attributes sort order.
     *
     * @api
     * @return int
     */
    public function getSortOrder();

    /**
     * Get attributes sort order.
     *
     * @api
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get label which supposed to be displayed on frontend.
     *
     * @api
     * @return string
     */
    public function getFrontendLabel();

    /**
     * Set label which supposed to be displayed on frontend.
     *
     * @api
     * @param string $frontendLabel
     * @return $this
     */
    public function setFrontendLabel($frontendLabel);

    /**
     * Get the note attribute for the element.
     *
     * @api
     * @return string
     */
    public function getNote();

    /**
     * Set the note attribute for the element.
     *
     * @api
     * @param string $note
     * @return $this
     */
    public function setNote($note);

    /**
     * Whether this is a system attribute.
     *
     * @api
     * @return bool
     */
    public function isSystem();

    /**
     * Set whether this is a system attribute.
     *
     * @api
     * @param bool $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem);

    /**
     * Get backend type.
     *
     * @api
     * @return string
     */
    public function getBackendType();

    /**
     * Set backend type.
     *
     * @api
     * @param string $backendType
     * @return $this
     */
    public function setBackendType($backendType);
}
