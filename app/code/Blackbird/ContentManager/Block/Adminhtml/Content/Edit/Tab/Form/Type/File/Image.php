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

/**
 * Build renderer as the core field type 'image'.
 *
 * Class Image
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File
 */
class Image extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\File\AbstractFile
{
    /**
     * Template to use saved in attribute
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit/tab/form/type/file/image.phtml';

    /**
     * Get Field Original filename
     *
     * @return string
     */
    public function getOrigFileName()
    {
        $data = $this->getContentField();
        $filename = '';

        if (isset($data[$this->getCustomField()->getIdentifier() . '_orig'])) {
            $filename = $data[$this->getCustomField()->getIdentifier() . '_orig'];
        }

        return $filename;
    }

    /**
     * Retrieves the params for the js script
     *
     * @return string
     */
    public function getJsonParams()
    {
        $data = [
            'image' => $this->getOrigImageUrl(),
            'ghost' => false,
            'originalsize' => false,
            'ajax' => false,
            'resize' => true,
            'editstart' => true,
            'saveOriginal' => true,
            'editcrop' => false,
            'width' => $this->getCustomField()->getCropW() ?: '400',
            'height' => $this->getCustomField()->getCropH() ?: '350',
        ];

        // If crop tool is disabled
        if (!$this->getCustomField()->getCrop()) {
            $data['buttonEdit'] = false;
            $data['buttonZoomin'] = false;
            $data['buttonZoomout'] = false;
            $data['buttonZoomreset'] = false;
            $data['buttonCancel'] = true;
            $data['buttonDone'] = true;
            $data['buttonDel'] = true;
            $data['editstart'] = false;
            $data['image'] = $this->getOrigImageUrl();
        } else {
            $data['image'] = $this->getCropedImageUrl();
            $data['buttonZoomin'] = false;
            $data['buttonZoomout'] = false;
            $data['editcrop'] = true;
        }

        if (!empty($data['image'])) {
            $data['image'] .= '?v=' . time();
        }

        return json_encode($data);
    }

    /**
     * Get Field Original full image url
     *
     * @return string
     */
    public function getOrigImageUrl()
    {
        $data = $this->getContentField();
        $filename = isset($data[$this->getCustomField()->getIdentifier() . '_orig']) ? $data[$this->getCustomField()
            ->getIdentifier() . '_orig'] : '';
        $path = '';

        if (!empty($filename)) {
            $path = ContentType::CT_FILE_FOLDER . $this->getAdditionalPath();
            $path = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . $filename;
        }

        return $path;
    }

    /**
     * Get croped image url
     *
     * @return string
     */
    public function getCropedImageUrl()
    {
        $data = $this->getContentField();
        $filename = isset($data[$this->getCustomField()->getIdentifier()]) ? $data[$this->getCustomField()
            ->getIdentifier()] : '';
        $path = '';

        if (!empty($filename)) {
            $path = ContentType::CT_FILE_FOLDER . ContentType::CT_IMAGE_CROPPED_FOLDER . $this->getAdditionalPath();
            $path = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . $filename;
        }

        return $path;
    }
}
