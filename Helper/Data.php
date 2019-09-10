<?php
/**
 * Phoenix_MediaStorageSync for Magento 2
 *
 *
 * @category    Phoenix
 * @package     Phoenix_MediaStorageSync
 * @license     http://opensource.org/licenses/MIT MIT
 * @copyright   Copyright (c) 2018 PHOENIX MEDIA GmbH (http://www.phoenix-media.eu)
 */

namespace Phoenix\MediaStorageSync\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Product\Media\Config as ProductMediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    /**
     * @var ProductMediaConfig
     */
    protected $productMediaConfig;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductMediaConfig $productMediaConfig
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        ProductMediaConfig $productMediaConfig,
        DirectoryList $directoryList
    ){
        parent::__construct($context);
        $this->productMediaConfig = $productMediaConfig;
        $this->directoryList = $directoryList;
    }

    /**
     * @return string
     */
    public function getCatalogMediaConfigPath()
    {
        return $this->productMediaConfig->getBaseMediaPath();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaBaseDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $completePath
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getAssetPath($completePath)
    {
        $assetPath = str_replace(
            $this->directoryList->getPath(DirectoryList::PUB),
            '',
            $completePath
        );

        return $assetPath;
    }

    /**
     * @param $file
     * @return bool
     */
    public function fileIsNotAvailable($file)
    {
        $fileIsNotAvailable = true;

        if (strpos($file, 'no_selection') !== false) {
            $file = null;
            $fileIsNotAvailable = false;
        }

        if ($file && file_exists($file)) {
            $fileIsNotAvailable = false;
        }

        return $fileIsNotAvailable;
    }
}
