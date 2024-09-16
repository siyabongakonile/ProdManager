<?php
declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Tag;
use PHPUnit\Framework\TestCase;
use App\Exceptions\ModelExceptions\EmptyTagNameException;
use App\Exceptions\ModelExceptions\EmptyTagSlugException;

class TagTest extends TestCase{
    private Tag $tag;

    public function setUp(): void{
        $this->tag = new Tag(1, 'tag', 'tag');
    }

    /** @test */
    public function raiseExceptionWhenNameSetToEmptyStr(){
        $this->expectException(EmptyTagNameException::class);
        $this->tag->setName('');
    }

    /** @test */
    public function setTagName(){
        $expected = 'New tag name';
        $this->tag->setName($expected);
        $this->assertSame($expected, $this->tag->getName());
    }

    /** @test */
    public function raiseExceptionWhenSlugSetToEmptyStr(){
        $this->expectException(EmptyTagSlugException::class);
        $this->tag->setSlug('');
    }

    /** @test */
    public function setTagSlug(){
        $expected = 'New tag slug';
        $this->tag->setSlug($expected);
        $this->assertSame($expected, $this->tag->getSlug());
    }
}