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

namespace Phoenix\MediaStorageSync\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Phoenix\MediaStorageSync\Model\Sync;
use Phoenix\MediaStorageSync\Model\Config;
use Phoenix\MediaStorageSync\Helper\Data as Helper;

class CatalogProductLoadAfter implements ObserverInterface
{
    protected $galleryReadHandler;
    protected $sync;
    protected $configModel;
    protected $helper;

    /**
     * ProductObserver constructor.
     * @param GalleryReadHandler $galleryReadHandler
     * @param Sync $syncModel
     * @param Config $configModel
     * @param Helper $helper
     */
    public function __construct(
        GalleryReadHandler $galleryReadHandler,
        Sync $syncModel,
        Config $configModel,
        Helper $helper
    )
    {
        $this->galleryReadHandler = $galleryReadHandler;
        $this->sync = $syncModel;
        $this->configModel = $configModel;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->configModel->isEnabled()) {
            $product = $observer->getEvent()->getProduct();

            $this->galleryReadHandler->execute($product);
            $mediaGalleryImages = $product->getMediaGalleryImages();

            if (!empty($mediaGalleryImages)) {
                foreach ($mediaGalleryImages as $mediaGalleryImage) {
                    $file = $mediaGalleryImage->getData('path');
                    $fileIsNotAvailable = $this->helper->fileIsNotAvailable($file);

                    if ($fileIsNotAvailable) {
                        $this->sync->sync(
                            $this->helper->getCatalogMediaConfigPath() . $mediaGalleryImage->getData('file'),
                            $this->helper->getMediaBaseDir()
                        );
                    }
                }
            }
        }
    }
}
