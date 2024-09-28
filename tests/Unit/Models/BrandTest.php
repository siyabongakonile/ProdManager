<?php
declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Exceptions\ModelExceptions\EmptyBrandNameException;
use PHPUnit\Framework\TestCase;

use App\Models\Brand;
use App\Exceptions\ModelExceptions\IdLessThanOneException;

class BrandTest extends TestCase{
    private ?Brand $brand = null;

    public function setup(): void{
        parent::setup();
        $this->brand = new Brand(null, "brand name");
    }

    /** @test */
    public function raisesExceptionWhenIdIsLessThanOne(){
        $this->expectException(IdLessThanOneException::class);
        $brand =  new Brand(-5, "name");
    }

    /** @test */
    public function raisesExceptionWhenIdSetToLessThanOne(){
        $this->expectException(IdLessThanOneException::class);
        $this->brand->setId(-1);
    }

    /** @test */
    public function raiseExceptionWhenNameSetToEmptyStr(){
        $this->expectException(EmptyBrandNameException::class);
        $this->brand->setName('');
    }

    /** @test */
    public function itGetsId() {
        $brand = new Brand(34, "test");
        $this->assertEquals(34, $brand->getId());
    }

    /** @test */
    public function itGetsNullId(){
        $this->assertNull($this->brand->getId());
    }

    /** @test */
    public function itSetsId(){
        $id = 5;
        $this->brand->setId($id);
        $this->assertEquals($id, $this->brand->getId());
    }

    /** @test */
    public function itSetsNullId(){
        $id = null;
        $this->brand->setId($id);
        $this->assertNull($id);
    }

    /** @test */
    public function itGetsName(){
        $this->assertEquals("brand name", $this->brand->getName());
    }

    /** @test */
    public function itSetsName(){
        $name = 'new name';
        $this->brand->setName($name);
        $this->assertEquals($name, $this->brand->getName());
    }
}