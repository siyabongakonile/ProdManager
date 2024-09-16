<?php 
declare(strict_types = 1);

namespace Tests\Unit\Models;
use App\Models\Product;
use App\Models\Products;
use PHPUnit\Framework\TestCase;

class ProductsTest extends TestCase{
    private Products $products;

    public function setUp(): void{
        $this->products = new Products();
    }

    /** @test */
    public function addProduct(){
        $product = $this->createMock(Product::class);
        $this->products->add($product);

        $this->assertEquals([$product], $this->products->toArray());
    }

    /** @test */
    public function removeProduct(){
        $product = $this->createMock(Product::class);
        $this->products->add($product);
        $this->products->remove($product);

        $this->assertEmpty($this->products->toArray());
    }

    /** @test */
    public function raiseExceptionWhenRemoveAtIsOutOfBound(){
        $prod = $this->createMock(Product::class);
        $this->products->add($prod);
        $this->expectException(\OutOfBoundsException::class);
        $this->products->removeAt(1);
    }

    /** @test */
    public function removeAt(){
        $prod = $this->createMock(Product::class);
        $this->products->add($prod);
        $this->products->removeAt(0);

        $this->assertEmpty($this->products->toArray());
    }
}