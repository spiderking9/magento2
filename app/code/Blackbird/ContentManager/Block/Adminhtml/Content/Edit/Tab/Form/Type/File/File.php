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

use Blackbird\ContentManager\Model\ContentType;
use Magento\Framework\UrlInterface;

/**
 * Build renderer as the core field type 'image'.
 * It made able to preview and delete the file
 *
 * Class File
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File
 */
class File extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File\AbstractFile
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit/tab/form/type/file/file.phtml';

    /**
     * Get field file url
     *
     * @return string
     */
    public function getFileUrl()
    {
        $filename = $this->_contentField[$this->getCustomField()->getIdentifier()];
        $path = ContentType::CT_FILE_FOLDER . '/' . $this->getAdditionalPath() . '/';

        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path . $filename;
    }

    /**
     * Retrieves the additional path where the images are saved
     *
     * @return string
     */
    protected function getAdditionalPath()
    {
        return $this->getCustomField()->getData('file_path') ?: '';
    }
}
