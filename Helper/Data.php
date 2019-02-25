<?php
/**
 * Phoenix_MediaStorageSync for Magento 2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to license that is bundled with
 * this package in the file LICENSE_$MODULE_NAME.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Phoenix_MediaStorageSync to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Phoenix
 * @package     Phoenix_MediaStorageSync
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
            $this->directoryList->getPath(DirectoryList::ROOT),
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
