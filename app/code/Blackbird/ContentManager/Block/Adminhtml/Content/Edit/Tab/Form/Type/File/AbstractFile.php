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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File;

/**
 * Build renderer as the core field type abstract 'file'
 *
 * Class AbstractFile
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File
 */
abstract class AbstractFile extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\AbstractType
{
    /**
     * Retrieves the additional path where the images are saved
     *
     * @return string
     */
    protected function getAdditionalPath()
    {
        $path = '';

        // Retrieve the additional file path
        if (!empty($this->getCustomField()->getData('file_path'))) {
            $path = $this->getCustomField()->getData('file_path');
            if (substr($path, -1) !== '/') {
                $path .= '/';
            }
        }

        return $path;
    }
}
