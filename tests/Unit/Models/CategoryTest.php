<?php
declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\CategoryModel;
use PHPUnit\Framework\TestCase;
use Exceptions\ModelExceptions\EmptyCategorySlugExcption;
use App\Exceptions\ModelExceptions\EmptyCategoryNameException;

class CategoryTest extends TestCase{
    private ?Category $category = null;

    public function setup(): void{
        $this->category = new Category(1, "test", "test");
    }

    /** @test */
    public function getId(){
        $this->assertSame(1, $this->category->getId());
    }

    /** @test */
    public function setId(){
        $this->category->setId(100);
        $this->assertSame(100, $this->category->getId());
    }

    /** @test */
    public function raiseExceptionWhenNameSetToEmptyString(){
        $this->expectException(EmptyCategoryNameException::class);
        $this->category->setName("");
    }

    /** @test */
    public function setCategoryName(){
        $catName = "testCatName";

        $this->category->setName($catName);

        $this->assertEquals($catName, $this->category->getName());
    }

    /** @test */
    public function raiseExceptionWhenSetSlugToEmpty(){
        $this->expectException(EmptyCategorySlugExcption::class);
        $this->category->setSlug('');
    }

    /** @test */
    public function setSlug(){
        $expected = 'New slug';
        $this->category->setSlug($expected);

        $this->assertSame($expected, $this->category->getSlug());
    }

    /** @test */
    public function getNullWhenParentIdIsSetToNull(){
        $categoryModelMock = $this->createMock(CategoryModel::class);

        $this->assertNull($this->category->getParentObject($categoryModelMock));
    }

    /** @test */
    public function getParentObject(){
        $this->category->setParentId(10);
        $expectedCategory = $this->createMock(Category::class);
        $categoryModelMock = $this->createConfiguredMock(CategoryModel::class, ['getCategory' => $expectedCategory]);

        $this->assertEquals($expectedCategory, $this->category->getParentObject($categoryModelMock));
    }
}