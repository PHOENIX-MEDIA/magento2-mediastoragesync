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

namespace Phoenix\MediaStorageSync\Model;

use Phoenix\MediaStorageSync\Helper\Data as Helper;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Logger\Handler\Exception;
use Psr\Log\LoggerInterface;

class Sync
{
    const PUB_DIRECTORY = 'pub';

    protected $file;
    protected $config;
    protected $logger;
    protected $exception;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var ClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var ClientInterface
     */
    protected $client;


    /**
     * Sync constructor.
     * @param File $file
     * @param Config $config
     * @param Helper $helper
     * @param ClientFactory $httpClientFactory
     * @param LoggerInterface $logger
     * @param Exception $exception
     */
    public function __construct(
        File $file,
        Config $config,
        Helper $helper,
        ClientFactory $httpClientFactory,
        LoggerInterface $logger,
        Exception $exception
    )
    {
        $this->file = $file;
        $this->config = $config;
        $this->helper = $helper;
        $this->httpClientFactory = $httpClientFactory;
        $this->logger = $logger;
        $this->exception = $exception;
    }

    /**
     * @param string $src
     * @param string $target
     * @return bool|int
     */
    public function sync($src, $target)
    {
        $result = false;
        if ($src && $target) {
            $result = $this->saveFileFromRemoteServer($src, $target);
        }
        return $result;
    }

    /**
     * @param string $src
     * @param string $target
     * @return bool
     */
    protected function saveFileFromRemoteServer($src, $target)
    {
        $fileSaved = false;
        $fileName = basename($src);
        $fileDirectory = $target;
        if ($fileName != $src) {
            $fileDirectory .= dirname($src);
        }

        try {
            $this->getFileFromServer($src, $target);
            if ($this->getHttpClient()->getStatus() == 200) {
                $this->file->setAllowCreateFolders(true);
                $this->file->open(array('path' => $fileDirectory));
                $fileSaved = $this->file->write($fileName, $this->getHttpClient()->getBody());
                $this->file->close();
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $fileSaved;
    }

    /**
     * @param string $src
     * @param string $target
     * @return void
     */
    protected function getFileFromServer($src, $target)
    {
        try {
            $fileUri = $this->config->getUrl()
                . $this->helper->getAssetPath($target)
                . $src;

            $this->getHttpClient()->get($fileUri);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        if (is_null($this->client)) {
            $client = $this->httpClientFactory->create();

            $client->setTimeout(20);
            $client->addHeader('User-Agent', 'Phoenix MediaStorageSync');
            $client->addHeader('Content-Transfer-Encoding', 'binary');

            $user = $this->config->getHttpClientUser();
            $password = $this->config->getHttpClientPassword();
            if ($user && $password) {
                $client->setCredentials($user, $password);
            }

            $this->client = $client;
        }

        return $this->client;
    }
}
