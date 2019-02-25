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

namespace Phoenix\MediaStorageSync\Plugin;

use Magento\MediaStorage\Model\File\Storage\Database as StorageDatabase;
use Phoenix\MediaStorageSync\Model\Config;
use Phoenix\MediaStorageSync\Model\Sync;
use Phoenix\MediaStorageSync\Helper\Data as Helper;


class Synchronization
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Sync
     */
    protected $sync;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Synchronization constructor.
     * @param Config $config
     * @param Sync $sync
     * @param Helper $helper
     */
    public function __construct(
        Config $config,
        Sync $sync,
        Helper $helper
    ) {
        $this->config = $config;
        $this->sync = $sync;
        $this->helper = $helper;
    }

    /**
     * @param StorageDatabase $subject
     * @param callable $proceed
     * @param string $filePath
     * @return StorageDatabase
     */
    public function aroundLoadByFilename(StorageDatabase $subject, callable $proceed, $filePath)
    {
        if ($this->config->isEnabled()) {
            try {
                if ($this->sync->sync($filePath, $this->helper->getMediaBaseDir())) {
                    $subject->setId(1);
                    $subject->setContent($this->sync->getHttpClient()->getBody());
                }
                return $subject;
            } catch (\Exception $e) {
                // do nothing
            }
        }
        return $proceed($filePath);
    }
}