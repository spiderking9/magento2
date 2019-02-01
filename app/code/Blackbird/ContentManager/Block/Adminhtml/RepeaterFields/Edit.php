<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package        Blackbird_ContentManager
 * @copyright           Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author        Blackbird Team
 * @license        http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Block\Adminhtml\RepeaterFields;

/**
 * Content type edit form block
 */
class Edit extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit
{
    /**
     * {@inheritdoc}
     */
    public $entityName = 'Repeater Fields';

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->removeButton('export-button');
    }
}
