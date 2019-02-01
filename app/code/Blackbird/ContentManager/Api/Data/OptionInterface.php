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
 * Interface OptionInterface
 *
 * @package Blackbird\ContentManager\Api\Data
 * @api
 * @todo NEED CHECKUP
 */
interface OptionInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const LABEL = 'label';
    const VALUE = 'value';
    const OPTIONS = 'options';
    /**#@-*/

    /**
     * Get option label
     *
     * @api
     * @return string
     */
    public function getLabel();

    /**
     * Set option label
     *
     * @api
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get option value
     *
     * @api
     * @return string|null
     */
    public function getValue();

    /**
     * Set option value
     *
     * @api
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get nested options
     *
     * @api
     * @return \Blackbird\ContentManager\Api\Data\OptionInterface[]|null
     */
    public function getOptions();

    /**
     * Set nested options
     *
     * @api
     * @param \Blackbird\ContentManager\Api\Data\OptionInterface[] $options
     * @return $this
     */
    public function setOptions(array $options = null);
}
