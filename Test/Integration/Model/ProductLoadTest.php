<?php


namespace Phoenix\MediaStorageSync\Test\Integration\Model;


use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Catalog\ProductFixtureRollback;

class ProductLoadTest extends \PHPUnit\Framework\TestCase
{

    /** @var ProductFixture */
    protected $productFixture;


    protected function setUp()
    {
        $this->productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withName("Product Without Image")
                ->withSku("test-without-image")
                ->build()
        );
    }

    protected function tearDown()
    {
        ProductFixtureRollback::create()->execute($this->productFixture);
    }


    public function testProductLoad()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $product \Magento\Catalog\Model\Product */
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($this->productFixture->getId());
        $this->assertSame("test-without-image", $product->getSku());
    }

}
