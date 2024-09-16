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
}