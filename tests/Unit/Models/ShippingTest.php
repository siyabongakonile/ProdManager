<?php
declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Exceptions\ModelExceptions\NegativePriceException;
use App\Models\Shipping;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase{
    private Shipping $shipping;

    public function setUp(): void{
        $this->shipping = new Shipping();
    }
    
    /** @test */
    public function raiseExceptionWhenLocalSetToNegativeValue(){
        $this->expectException(NegativePriceException::class);
        $this->shipping->setLocal(-1);
    }

    /** @test */
    public function setLocalNull(){
        $this->shipping->setLocal(null);
        $this->assertNull($this->shipping->getLocal());
    }

    /** @test */
    public function setLocal(){
        $expected = 100.0;
        $this->shipping->setLocal($expected);
        $this->assertSame($expected, $this->shipping->getLocal());
    }

    /** @test */
    public function raiseExceptionWhenNationwideSetToNegativeValue(){
        $this->expectException(NegativePriceException::class);
        $this->shipping->setLocal(-1);
    }

    /** @test */
    public function setNationwideNull(){
        $this->shipping->setLocal(null);
        $this->assertNull($this->shipping->getLocal());
    }

    /** @test */
    public function setNationwide(){
        $expected = 100.0;
        $this->shipping->setLocal($expected);
        $this->assertSame($expected, $this->shipping->getLocal());
    }

    /** @test */
    public function raiseExceptionWhenInternationalSetToNegativeValue(){
        $this->expectException(NegativePriceException::class);
        $this->shipping->setLocal(-1);
    }

    /** @test */
    public function setInternationalNull(){
        $this->shipping->setLocal(null);
        $this->assertNull($this->shipping->getLocal());
    }

    /** @test */
    public function setInternational(){
        $expected = 100.0;
        $this->shipping->setLocal($expected);
        $this->assertSame($expected, $this->shipping->getLocal());
    }
}