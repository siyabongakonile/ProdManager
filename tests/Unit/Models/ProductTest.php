<?php
declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Tag;

use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Brand;
use App\Models\Shipping;

use PHPUnit\Framework\TestCase;
use App\Exceptions\ModelExceptions\IdLessThanOneException;
use App\Exceptions\ModelExceptions\EmptyProductNameException;
use App\Exceptions\ModelExceptions\NegativeProductPriceException;
use App\Exceptions\ModelExceptions\InvalidProductImageURLException;
use App\Exceptions\ModelExceptions\EmptyProductDescriptionException;
use App\Exceptions\ModelExceptions\ProductQuantityLessThanZeroException;

class ProductTest extends TestCase{
    private ?Product $product = null;

    public function setup(): void{
        parent::setup();

        $this->product = new Product(
            1,
            "product name",
            "code",
            "desc",
            100.00
        );
    }

    /**
     * @test
     * @dataProvider getNonNaturalNums
     */
    public function raiseExceptionWhenIdSetToLessThanOne(int $id){
        $this->expectException(IdLessThanOneException::class);
        $this->product->setId($id);
    }

    /**
     * @test
     * @dataProvider getNaturalIds
     */
    public function getTheProductId(?int $naturalId){
        $this->product->setId($naturalId);
        $id = $this->product->getId();
        $this->assertSame($naturalId, $id);
    }

    /** @test */
    public function setProductIdToInt(){
        $this->product->setId(13);
        $this->assertSame(13, $this->product->getId());
    }

    /** @test */
    public function setProductIdToNull(){
        $this->product->setId(null);
        $this->assertNull($this->product->getId());
    }

    /** @test */
    public function getProductName(){
        $prodName = $this->product->getName();
        $this->assertSame("product name", $prodName);
    }

    /** @test */
    public function setProductName(){
        $testName = "test name";
        $this->product->setName($testName);
        $this->assertSame($testName, $this->product->getName());
    }

    /** @test */
    public function raiseExceptionWhenNameSetToEmpty(){
        $this->expectException(EmptyProductNameException::class);
        $this->product->setName('');
    }

    /**
     * @test
     * @dataProvider getValidCodeValues
     */
    public function setCodePropertyMethods(?string $code){
        $this->product->setCode($code);
        $expectedValue = \trim($code);
        $this->assertSame($expectedValue, $this->product->getCode());
    }

    /** @test */
    public function itGetsCode(){
        $this->assertSame('code', $this->product->getCode());
    }

    /** @test */
    public function itSetsCode(){
        $expected = 'new code';
        $this->product->setCode($expected);
        $this->assertSame($expected, $this->product->getCode());
    }

    /** @test */
    public function itGetsDescription(){
        $this->assertSame('desc', $this->product->getDescription());
    }

    /** @test */
    public function raiseExceptionWhenDescriptionSetToEmpty(){
        $this->expectException(EmptyProductDescriptionException::class);
        $this->product->setDescription('');
        $this->product->setDescription('   ');
    }

    /** @test */
    public function raiseExceptionWhenDescriptionSetToEmptySpaces(){
        $this->expectException(EmptyProductDescriptionException::class);
        $this->product->setDescription('   ');
    }

    /** @test */
    public function setDescription(){
        $desc = 'test desc';
        $this->product->setDescription($desc);
        $testDesc = $this->product->getDescription();
        $this->assertSame($desc, $testDesc);
    }

    /** @test */
    public function itGetsPrice(){
        $this->assertSame(100.0, $this->product->getPrice());
    }

    /** @test */
    public function raiseExceptionWhenPriceSetToNegative(){
        $this->expectException(NegativeProductPriceException::class);
        $this->product->setPrice(-100);
    }

    /** @test */
    public function setPrice(){
        $price = 102.0;
        $this->product->setPrice($price);
        $testedPrice = $this->product->getPrice();
        $this->assertSame($price, $testedPrice);
    }

    /** @test */
    public function itGetsCondition(){
        $this->assertSame(Condition::New, $this->product->getCondition());
    }

    /** @test */
    public function itSetsCondition(){
        $expected = Condition::NotWorking;
        $this->product->setCondition($expected);
        $this->assertSame($expected, $this->product->getCondition());
    }

    /** @test */
    public function itGetsNullBrand(){
        $this->assertNull($this->product->getBrand());
    }

    /** @test */
    public function itGetsBrandObj(){
        $brandMock = $this->createMock(Brand::class);
        $this->product->setBrand($brandMock);
        $this->assertEquals($brandMock, $this->product->getBrand());
    }

    /** @test */
    public function itSetsBrandToNull(){
        $this->product->setBrand(null);
        $this->assertNull($this->product->getBrand());
    }

    /** @test */
    public function itSetsBrandToBrandObj(){
        $brandMock = $this->createMock(Brand::class);
        $this->product->setBrand($brandMock);
        $this->assertEquals($brandMock, $this->product->getBrand());
    }

    /** @test */
    public function getEmptyCategories(){
        $this->assertEmpty($this->product->getCategories());
    }

    /** @test */
    public function setCategories(){
        // Categories that will be replaced by new ones.
        $oldCat = $this->createMock(Category::class);
        $this->product->setCategories($oldCat);

        // New categories.
        $cat = $this->createMock(Category::class);
        $cat2 = $this->createConfiguredMock(Category::class, ['getId' => 2]);
        $cat3 = $this->createConfiguredMock(Category::class, ['getId' => 10]);

        // Set the new categories and check if the only one in the 
        // product are the new ones.
        $this->product->setCategories($cat, $cat2, $cat3);
        $testedCats = $this->product->getCategories();
        $this->assertContainsOnlyInstancesOf(Category::class, $testedCats);
        $this->assertEquals([$cat, $cat2, $cat3], $this->product->getCategories());
    }

    /**
     * When adding an existing category the new category
     * will not be added resulting in the product categories
     * length being the same before and after adding the category
     * 
     * @test
     */
    public function addAnExistingCategoryButGetUniqueOnes(){
        $category = $this->createMock(Category::class);
        $this->product->setCategories($category);
        $beforeAddingLength = count($this->product->getCategories());
        $this->product->addCategory($category);

        $afterAddingLength = count($this->product->getCategories());
        $this->assertEquals($beforeAddingLength, $afterAddingLength);
    }

    /** @test */
    public function addCategory(){
        $oldCat = $this->createConfiguredMock(Category::class, ['getId' => 1]);
        $this->product->setCategories($oldCat);
        
        $cats = $this->product->getCategories();
        
        $newCat = $this->createConfiguredMock(Category::class, ['getId' => 10]);
        $this->product->addCategory($newCat);
        $cats[] = $newCat;
        $this->assertEquals($cats, $this->product->getCategories());
    }

    /** @test */
    public function removeCategory(){
        $cat = $this->createMock(Category::class);
        $this->product->addCategory($cat);

        $catsBeforeRemoving = $this->product->getCategories();

        $result = $this->product->removeCategory($cat);
        $catsAfterRemoving = $this->product->getCategories();

        $this->assertEquals($cat, $result);
        $this->assertCount(1, $catsBeforeRemoving);
        $this->assertCount(0, $catsAfterRemoving);
    }

    /** @test */
    public function returnNullIfCategoryToRemoveDoesNotExist(){
        $cat = $this->createMock(Category::class);
        $result = $this->product->removeCategory($cat);
        $this->assertNull($result);
    }

    /** @test */
    public function getEmptyArrayOfTags(){
        $this->assertEmpty($this->product->getTags());
    }

    /** @test */
    public function setTags(){
        $tags = [
            $this->createConfiguredMock(Tag::class, ['getId' => 1]),
            $this->createConfiguredMock(Tag::class, ['getId' => 2]),
            $this->createConfiguredMock(Tag::class, ['getId' => 3])
        ];
        $oldTags = $this->product->getTags();

        $this->product->setTags(...$tags);
        
        $testedTags = $this->product->getTags();
        $this->assertEquals($tags, $testedTags);
    }

    /** @test */
    public function addTags(){
        $tags = [
            $this->createConfiguredMock(Tag::class, ['getId' => 1]),
            $this->createConfiguredMock(Tag::class, ['getId' => 2]),
            $this->createConfiguredMock(Tag::class, ['getId' => 3])
        ];
        $oldTags = $this->product->getTags();
        $expected = [...$oldTags, ...$tags];
        $expected = \array_unique($expected, SORT_REGULAR);

        $this->product->addTags(...$tags);

        $this->assertEquals($expected, $this->product->getTags());
    }

    /** @test */
    public function addTag(){
        $tags = [
            $this->createConfiguredMock(Tag::class, ['getId' => 1]),
            $this->createConfiguredMock(Tag::class, ['getId' => 2]),
            $this->createConfiguredMock(Tag::class, ['getId' => 3])
        ];
        $this->product->setTags(...$tags);
        $tags = $this->product->getTags();
        $newTag = $this->createMock(Tag::class);

        $this->product->addTag($newTag);

        $tags[] = $newTag;
        $this->assertEquals($tags, $this->product->getTags());
    }

    /** @test */
    public function itIsInStockWhenQuantitySetToMoreThanZero(){
        $this->product->setQuantity(10);
        $this->assertTrue($this->product->getIsInstock());
    }

    /** @test */
    public function itIsNotInStockWhenQuantitySetToZero(){
        $this->product->setQuantity(0);
        $this->assertFalse($this->product->getIsInstock());
    }

    /** 
     * @test
     * @dataProvider getValidUrls
     */
    public function addImage(string $imgUrl){
        $this->product->clearImages();

        $this->product->addImage($imgUrl);

        $this->assertEquals([$imgUrl], $this->product->getImages());
    }

    /** 
     * @test
     * @dataProvider getInvalidUrls
     */
    // public function raiseExceptionWhenImageSetToInvalidUrl(string $image){
    //     $this->expectException(InvalidProductImageURLException::class);
    //     $this->product->addImage($image);
    // }

    /** @test */
    public function addImages(){
        $this->product->clearImages();
        $imgs = [
            1,
            'http://google.com',
            new \StdClass()
        ];
        $expected = 'http://google.com';

        $this->product->addImages($imgs);

        $this->assertEquals([$expected], $this->product->getImages());
    }

    /** @test */
    public function removeImage(){
        $this->product->clearImages();
        $imgs = [
            "https://startervista.co.za",
            "http://google.com/index",
            "http://prodmanager.mvc/test.php"
        ];
        $this->product->setImages($imgs);

        $this->product->removeImage($imgs[0]);

        $expected = \array_slice($imgs, 1);
        $this->assertEquals($expected, $this->product->getImages());
    }

    /** @test */
    public function removeImages(){
        $this->product->clearImages();
        $imgs = [
            "https://startervista.co.za",
            "http://google.com/index",
            "http://prodmanager.mvc/test.php"
        ];
        $this->product->setImages($imgs);

        $this->product->removeImages([$imgs[0], 2, "fdsfs", '', new \StdClass()]);

        $expected = \array_slice($imgs, 1);
        $this->assertEquals($expected, $this->product->getImages());
    }

    /** @test */
    public function removeImageAt(){
        $imgs = [
            "https://startervista.co.za",
            "http://google.com/index",
            "http://prodmanager.mvc/test.php"
        ];
        $this->product->setImages($imgs);

        $this->product->removeImageAt(1);

        $expected = [
            "https://startervista.co.za",
            "http://prodmanager.mvc/test.php"
        ];
        $this->assertEquals($expected, $this->product->getImages());
    }

    /** 
     * @test 
     * @dataProvider getInvalidQuantities
     * */
    public function raiseExceptionWhenQuantitySetToLessThanZero(int $quantity){
        $this->expectException(ProductQuantityLessThanZeroException::class);
        $this->product->setQuantity($quantity);
    }

    /** @test */
    public function setQuantity(){
        $this->assertFalse(
            $this->product->getIsInstock(), 
            "The Product should not be instock because when 
            the it was instantiated the quantity was set to 0."
        );

        $this->product->setQuantity(10);

        $this->assertEquals(10, $this->product->getQuantity());
        $this->assertTrue($this->product->getIsInstock());
    }

    /** @test */
    public function itSetsShipping(){
        $shippingMock = $this->createConfiguredMock(
            Shipping::class, 
            [
                'getLocal' => 0.0, 
                'getNationwide' => 100.0, 
                'getInternational' => 1000.0
            ]
        );

        $this->product->setShipping($shippingMock);

        $this->assertSame($shippingMock, $this->product->getShipping());
    }

    #region Data Providers
    public function getNonNaturalNums(): array{
        return [
            [0],
            [-1],
            [-100]
        ];
    }

    public function getNaturalIds(): array{
        return [
            [null],
            [1],
            [1000]
        ];
    }

    public function getValidCodeValues(): array{
        return [
            [''],
            ['   '],
            ["test code"]
        ];
    }

    public function getInvalidUrls(): array{
        return [
            [''],
            ['    '],
            ['iffdg fsd sdf'],
            ['google'],
            ['google.com']
        ];
    }

    public function getValidUrls(): array{
        return [
            ["http://google.com"],
            ["https://prodmanager.mvc"],
            ["https://startervista.co.za/test.php"]
        ];
    }

    public function getInvalidQuantities(): array{
        return [
            [-1],
            [-100]
        ];
    }
    #endregion
}