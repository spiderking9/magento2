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

use Blackbird\ContentManager\Model\ContentType;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\UrlInterface;

/**
 * Content Manager image helper
 *
 * Class Image
 *
 * @package Blackbird\ContentManager\Helper
 */
class Image extends AbstractHelper
{
    const IMAGE_CACHE_DIR = 'cache/';

    const DEFAULT_QUALITY = 89;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Framework\Image
     */
    protected $_imageProcessor;

    /**
     * @var string
     */
    protected $_baseFile;

    /**
     * @var string
     */
    protected $_fullBaseFile;

    /**
     * @var string
     */
    protected $_imageCacheDestination;

    /**
     * Image constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * Init the helper
     *
     * @param string $file
     * @param string $baseDir
     * @return $this
     */
    public function init($file, $baseDir)
    {
        try {
            $this->setBaseFile($file, $baseDir);
            $filename = $this->_mediaDirectory->getAbsolutePath($this->getFullBaseFile());
            $this->_imageProcessor = $this->_imageFactory->create($filename);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Get full base file
     *
     * @return string
     */
    public function getFullBaseFile()
    {
        if (!empty($this->getBaseFile()) && empty($this->_fullBaseFile)) {
            $this->_fullBaseFile = str_replace('//', '/', ContentType::CT_FILE_FOLDER . $this->getBaseFile());
        }

        return $this->_fullBaseFile;
    }

    /**
     * Get base file
     *
     * @return string
     */
    public function getBaseFile()
    {
        return $this->_baseFile;
    }

    /**
     * Set base image file
     *
     * @param $file
     * @param $baseDir
     * @return $this
     * @throws \Exception
     */
    protected function setBaseFile($file, $baseDir)
    {
        $this->_baseFile = str_replace('//', '/', $baseDir . '/' . $file);
        $this->_fullBaseFile = '';

        if (!$file || !$this->_mediaDirectory->isExist($this->getFullBaseFile())) {
            $this->_baseFile = '';
            $this->_fullBaseFile = '';
            throw new \Exception(__('We can\'t find the image file.'));
        }

        return $this;
    }

    /**
     * Resize the current processed image
     *
     * @param int|null $width
     * @param int|null $height
     * @param bool $keepAspectRatio
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resize($width = null, $height = null, $keepAspectRatio = true)
    {
        $image = $this->getImage();
        $destination = $this->getCacheDestination($width . 'x' . $height . '/') . $this->getBaseFile();

        try {
            $image->constrainOnly(true);
            $image->quality(self::DEFAULT_QUALITY);
            $image->keepAspectRatio($keepAspectRatio);
            $image->keepTransparency(true);
            $image->resize($width, $height);
            $image->save($this->_mediaDirectory->getAbsolutePath($destination));
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $destination;
    }

    /**
     * Retrieve image
     *
     * @return \Magento\Framework\Image
     */
    public function getImage()
    {
        if (!$this->_imageProcessor) {
            $this->_imageProcessor = $this->_imageFactory->create();
        }

        return $this->_imageProcessor;
    }

    /**
     * Get cache destination folder
     *
     * @param string|null $subFolder
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getCacheDestination($subFolder = null)
    {
        $destinationCachePath = $this->getImageCacheDestination();
        if (!empty($subFolder)) {
            $destinationCachePath .= $subFolder;
            $this->_mediaDirectory->create($destinationCachePath);
        }

        return $destinationCachePath;
    }

    /**
     * Get cache destination for the image
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getImageCacheDestination()
    {
        if (!$this->_imageCacheDestination) {
            $destinationCache = ContentType::CT_FILE_FOLDER . self::IMAGE_CACHE_DIR;
            $this->_mediaDirectory->create($destinationCache);
            $this->_imageCacheDestination = $destinationCache;
        }

        return $this->_imageCacheDestination;
    }
}
