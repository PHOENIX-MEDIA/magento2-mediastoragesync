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
use Phoenix\MediaStorageSync\Model\Sync;
use Phoenix\MediaStorageSync\Model\Config;
use Phoenix\MediaStorageSync\Helper\Data as Helper;

class CatalogProductCollectionLoadAfter implements ObserverInterface
{
    protected $sync;
    protected $configModel;
    protected $helper;

    /**
     * ProductObserver constructor.
     * @param Sync $syncModel
     * @param Config $configModel
     * @param Helper $helper
     */
    public function __construct(
        Sync $syncModel,
        Config $configModel,
        Helper $helper
    ) {
        $this->sync         = $syncModel;
        $this->configModel  = $configModel;
        $this->helper       = $helper;
    }

    public function execute(Observer $observer)
    {
        if ($this->configModel->isEnabled()) {
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $collection = $observer->getEvent()->getCollection();
            foreach ($collection as $product) {
                $image = $product->getSmallImage();
                if (!empty($image)) {
                    $file = $this->helper->getMediaBaseDir() . 'catalog/product/' . $image;
                    $fileIsNotAvailable = $this->helper->fileIsNotAvailable($file);

                    if ($fileIsNotAvailable) {
                        $this->sync->sync(
                            'catalog/product/' . $image,
                            $this->helper->getMediaBaseDir()
                        );
                    }
                }
            }
        }
    }
}
