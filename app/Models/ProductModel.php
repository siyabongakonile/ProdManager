<?php
declare(strict_types = 1);

namespace App\Models;

use \App\Database;
use \App\Models\Product;
use \App\Models\Products;
use \App\Models\Category;
use \App\Models\Tag;
use \App\Models\Shipping;
use \App\Models\Condition;
use \App\Models\Brand;

class ProductModel extends BaseModel{
    public function __construct(Database $db){
        parent::__construct($db);
    }

    /**
     * Check a given product exists by its id.
     *
     * @param integer $prodId The id of the product ot check if exists
     * @return boolean True if the product exists or false otherwise
     */
    public function productExists(int $prodId): bool{
        $sql = "SELECT id FROM product WHERE id = {$prodId}";
        $res = $this->database->query($sql);
        if($res->num_rows > 0) return true;
        return false;
    }

    /**
     * Get every product in the database.
     * 
     * @param int $pageNum Page number
     * @param int $limit Number of items per page
     * 
     * @return Products
     */
    public function getAllProducts(int $pageNum, int $limit): Products{
        $sql = "SELECT * FROM product AS prod,
                brand AS br,
                shipping AS ship
                WHERE prod.brand_id = br.brand_id 
                AND prod.id = ship.prod_id";
        $result = $this->database->query($sql);

        $products = new Products();
        for($ctr = 0; $ctr < $result->num_rows; $ctr++){
            $row = $result->fetch_assoc();
            $products->add($this->convertDBProductToProduct($row));
        }
        return $products;
    }

    /**
     * Get a product using its id.
     * 
     * @param int $id The product's id
     * @return Product
     */
    public function getProduct(int $id): Product{
        $sql = "SELECT id FROM product WHERE id={$id}";
        $fullProductSql = $this->selectProductSql($sql);
        $result = $this->database->query($fullProductSql);
        return $this->convertDBProductToProduct($result->fetch_assoc());
    }

    /**
     * Select the products using an SQL code that returns product ids.
     * 
     * @param string $innerSql The sql code that returns product ids
     * @return string The SQL code that will return products from the database
     */
    private function selectProductSql(string $innerSql){
        return "SELECT DISTINCT * FROM product
        JOIN shipping ON product.id = shipping.prod_id 
        JOIN brand ON product.brand_id = brand.brand_id
        WHERE product.id IN ({$innerSql})";
    }

    /**
     * Get the products that meet the filters.
     * 
     * @param string|null $code The product code to search for
     * @param array|null $brands The list of brands to search products of
     * @param array|null $categories The list to categories to search products of
     * @param array|null $tags The list of tags to get products of
     * @param bool|null $isInstock 
     * @param bool|null $isPublished
     */
    public function getFilteredProducts(
        ?string $code = null,
        ?array $brands = null,
        ?array $categories = null,
        ?array $tags = null,
        ?bool $isInstock = null,
        ?bool $isPublished = null,
        int $pageNum = 1,
        int $limit = 10
    ): Products{
        $sql = $this->getFilteredProductsSql($code, $brands, $categories, $tags, $isInstock, $isPublished);
        if($sql == "") 
            return $this->getAllProducts($pageNum, $limit);

        $products = new Products();
        $res = $this->database->query($sql);
        while($row = $res->fetch_assoc()){
            $products->add($this->convertDBProductToProduct($row));
        }
        return $products;
    }

    /**
     * Get the SQL code for getting filtered products.
     * 
     * @param string|null $code The product code to search for
     * @param array|null $brands The list of brands to search products of
     * @param array|null $categories The list to categories to search products of
     * @param array|null $tags The list of tags to get products of
     * @param bool|null $isInstock 
     * @param bool|null $isPublished
     */
    public function getFilteredProductsSql(
        ?string $code = null,
        ?array $brands = null,
        ?array $categories = null,
        ?array $tags = null,
        ?bool $isInstock = null,
        ?bool $isPublished = null
    ): string{
        $sql            = "";
        $sqlCode        = "";
        $sqlBrands      = "";
        $sqlCategories  = "";
        $sqlTags        = "";
        $sqlIsInstock   = "";
        $sqlIsPublished = "";
        
        if($code !== null){
            $code = trim($code);
            $sqlCode = $this->getProductsByCode($code);
        }

        if($brands !== null){
            $brandIds = [];
            foreach($brands as $brand){
                if(get_class($brand) == Brand::getClass()){
                    $brandIds[] = $brand->id;
                }
            }
            $sqlBrands = $this->getProductsByBrands($brandIds);
        }

        if($categories !== null){
            $categoryIds = [];
            foreach($categories as $category){
                if(get_class($category) == Category::getClass()){
                    $categoryIds[] = $category->getId();
                }
            }
            $sqlCategories = $this->getProductsByCategories($categoryIds);
        }

        if($tags !== null){
            $tagIds = [];
            foreach($tags as $tag){
                if(get_class($tag) == Tag::getClass()){
                    $tagIds[] = $tag->getId();
                }
            }
            $sqlTags = $this->getProductsByTags($tagIds);
        }

        if($isInstock !== null){
            if($isInstock){
                $sqlIsInstock = $this->getInStockProducts();
            } else {
                $sqlIsInstock = $this->getOutOfStockProducts();
            }
        }

        if($isPublished !== null){
            echo "here";
            if($isPublished){
                $sqlIsPublished = $this->getPublishedProducts();
            } else {
                $sqlIsPublished = $this->getUnpublishedProducts();
            }
        }

        if($sqlCode != ""){
            $sql .= $sqlCode;
        }

        if($sqlBrands != ""){
            if($sql != "") $sql .= $this->getUnionStr();
            $sql .= $sqlBrands;
        }

        if($sqlCategories != ""){
            if($sql != "") $sql .= $this->getUnionStr();
            $sql .= $sqlCategories;
        }

        if($sqlTags != ""){
            if($sql != "") $sql .= $this->getUnionStr();
            $sql .= $sqlTags;
        }

        if($sqlIsInstock != ""){
            if($sql != "") $sql .= $this->getUnionStr();
            $sql .= $sqlIsInstock;
        }

        if($sqlIsPublished != ""){
            if($sql != "") $sql .= $this->getUnionStr();
            $sql .= $sqlIsPublished;
        }

        return $sql;
    }

    /**
     * Get the SQL code used to get products by code.
     * 
     * @param string $code The code used to filter the products
     * @return string The sql code
     */
    public function getProductsByCode(string $code): string{
        $sql = "SELECT DISTINCT * FROM product 
                JOIN shipping ON product.id = shipping.prod_id 
                JOIN brand ON product.brand_id = brand.brand_id
                WHERE product.code = '{$code}'";
        return $sql;
    }

    /**
     * Select products using brand id.
     * 
     * @param int $brandId
     * @return string
     */
    public function getProductsByBrand(int $brandId): string{
        $sql = "SELECT DISTINCT * FROM product
                JOIN shipping ON product.id = shipping.prod_id 
                JOIN brand ON product.brand_id = brand.brand_id
                WHERE brand.brand_id = {$brandId}";
        return $sql;
    }

    /**
     * Select products using brand ids.
     * 
     * @param array<int> $brandIds
     * @return string
     */
    public function getProductsByBrands(array $brandIds): string{
        $sql = "SELECT DISTINCT * FROM product
                JOIN shipping ON product.id = shipping.prod_id 
                JOIN brand ON product.brand_id = brand.brand_id
                WHERE brand.brand_id IN (" . \implode(", ", $brandIds) . ")";
        return $sql;
    }

    /**
     * Generate an sql query that get products based on the category id.
     * 
     * @param int $categryId
     * @return string The generated sql
     */
    public function getProductsByCategory(int $categoryId): string{
        $prodIdsSql = "SELECT prod_id FROM product_category WHERE cat_id = {$categoryId}";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Generate an sql query that get products based on the category ids.
     * 
     * @param array<int> $categryIds
     * @return string The generated sql
     */
    public function getProductsByCategories(array $categoryIds): string{
        $prodIdsSql = "SELECT prod_id FROM product_category 
                    WHERE cat_id IN (" . \implode(", ", $categoryIds) . ")";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Generate an sql query that get products based on the tag id.
     * 
     * @param int $tagId
     * @return string The generated sql
     */
    public function getProductsByTag(int $tagId): string{
        $prodIdsSql = "SELECT prod_id FROM product_tag WHERE tag_id = {$tagId}";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Generate an sql query that get products based on the tag ids.
     * 
     * @param array<int> $tagIds
     * @return string The generated sql
     */
    public function getProductsByTags(array $tagIds): string{
        $prodIdsSql = "SELECT prod_id FROM product_tag 
                    WHERE tag_id IN (" . \implode(", ", $tagIds) . ")";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     *  Get the SQL code for products that are out of stock.
     * 
     * @return string The SQL code for products that are out of stock
     */
    public function getOutOfStockProducts(): string{
        $prodIdsSql = "SELECT id FROM product WHERE product.quantity = 0";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Get the SQL code for products that are instock.
     * 
     * @return string The SQL code for products that are instock
     */
    public function getInStockProducts(): string{
        $prodIdsSql = "SELECT id FROM product WHERE product.quantity > 0";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Get SQL code for published products.
     * 
     * @return string The SQL for getting published products
     */
    public function getPublishedProducts(): string{
        $prodIdsSql = "SELECT id FROM product WHERE product.is_published = 1";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Get SQL code for unpublished products.
     * 
     * @return string The SQL for getting unpublished products
     */
    public function getUnpublishedProducts(): string{
        $prodIdsSql = "SELECT id FROM product WHERE product.is_published = 0";
        return $this->selectProductSql($prodIdsSql);
    }

    /**
     * Add a product to the database.
     * 
     * @param Product $product The product to add to the database
     * @return int The id of the added product
     */
    public function addProduct(Product $product){
        $name         = $product->getName();
        $code         = $product->getCode();
        $desc         = $product->getDescription();
        $price        = $product->getPrice();
        $isPublished  = $product->getIsPublished();
        $quantity     = $product->getQuantity();
        $brand        = $product->getBrand();
        $shipping     = $product->getShipping();
        $images       = $product->getImages();
        $categories   = $product->getCategories();
        $tags         = $product->getTags();
        $condition    = $product->getCondition();

        $brandId = $brand == null? null: $brand->getId();
        $this->addProductData($name, $code, $desc, $condition, $price, $isPublished, $quantity, $brandId);
        $id = $this->database->getInsertId();
        $this->addProductShipping($id, $shipping);
        $this->addProductCategories($id, $categories);
        $this->addProductTags($id, $tags);
        $this->addProductImages($id, $images);

        return $id;
    }

    private function addProductInfo(string $sql){
        // die($sql);
        $res = $this->database->query($sql);
        if(!$res){
            throw new \Exception("Unknown Error while adding the product.");
        }
    }

    /**
     * Add the data to the product's table.
     * 
     * @param string $name        The product name
     * @param string $code        The product code
     * @param string $desc        The product description
     * @param Condition $condition The product's condtion
     * @param float  $price       The product's price
     * @param bool   $isPublished Is the product published or is draft
     * @param int    $quantity    The product's quantity in stock
     * @param int    $brandId     The product brand's id
     */
    private function addProductData(
        string $name, 
        string $code, 
        string $desc, 
        Condition $condition,
        float $price, 
        bool $isPublished, 
        int $quantity, 
        ?int $brandId
    ){
        $cond = \strtolower($condition->name);
        $isPublishedSql = $this->database->convertBool($isPublished);
        $brandId = $brandId ?? 1;
        $sql = "INSERT INTO product (name, code, description, prod_condition, price, 
        is_published, quantity, brand_id) VALUES 
        ('{$name}', '{$code}', '{$desc}', '{$cond}', {$price}, {$isPublishedSql}, 
        {$quantity}, {$brandId})";
        $this->addProductInfo($sql);
    }

    /**
     * Add the product's categories.
     * 
     * @param int   $id         The product's id
     * @param array<Category> $categories The product's categories
     */
    private function addProductCategories(int $id, array $categories){
        foreach($categories as $category){
            $sql = "INSERT INTO product_category 
                    VALUES ({$id}, {$category->getId()})";
            $this->database->query($sql);
        }
    }

    /**
     * Add product's shipping data.
     * 
     * @param int $id The product's id
     * @param Shipping $shipping The product's shipping information
     */
    private function addProductShipping(int $id, Shipping $shipping){
        $sql = "INSERT INTO shipping values 
                ({$id}, {$shipping->getLocal()}, 
                {$shipping->getNationwide()}, 
                {$shipping->getInternational()})";
        $this->addProductInfo($sql);
    }

    /**
     * Add the product's tags.
     * 
     * @param int   $id   The product's id
     * @param array<Tag> $tags The product's tags
     */
    private function addProductTags(int $id, array $tags){
        foreach($tags as $tag){
            $sql = "INSERT INTO product_tag 
                    VALUES ({$id}, {$tag->getId()})";
            $this->database->query($sql);
        }
    }

    /**
     * Add product's images data.
     * 
     * @param int   $id     The product's id
     * @param array<string> $images The product's images
     */
    public function addProductImages(int $id, array $images){
        foreach($images as $image){
            $sql = "INSERT INTO product_image VALUES ($id, '$image')";
            $this->database->query($sql);
        }
    }

    /**
     * Get the image links for the product.
     * 
     * @param int $prodId The product id
     * @return array The array of the product's image links
     */
    private function getImages(int $prodId): array{
        $sql = "SELECT * FROM product_image WHERE prod_id = {$prodId}";
        $res = $this->database->query($sql);
        $images = [];
        
        for($ctr = 0; $ctr < $res->num_rows; $ctr++){
            $images[] = $res->fetch_assoc()['prod_image'];
        }

        return $images;
    }

    /**
     * Remove a given image form the database.
     * 
     * @param string $image The name of the image to remove
     * @return bool True if the image was successfully removed or false otherwise
     */
    public function removeImage(string $image): bool{
        $sql = "DELETE FROM product_image WHERE prod_image = '{$image}'";
        return ($this->database->query($sql))? true: false;
    }

    /**
     * Convert the database product array to a product object.
     * 
     * @param array $prodArr The database array to convert
     * @return Product The converted product
     */
    private function convertDBProductToProduct(array $prodArr): Product{
        $prodId = (int) $prodArr['id'];
        $brand  = new Brand((int) $prodArr['brand_id'], $prodArr['brand_name']);

        $local = ($prodArr['local'] == null)? null: (float) $prodArr['local'];
        $nationwide = ($prodArr['nationwide'] == null)? null: (float) $prodArr['nationwide'];
        $international = ($prodArr['international'] == null)? null: (float) $prodArr['international'];
        $shipping = new Shipping(
            $local, 
            $nationwide, 
            $international
        );

        $product = new Product(
            id: $prodId,
            name: $prodArr['name'],
            code: $prodArr['code'],
            description: $prodArr['description'],
            price: (float) $prodArr['price'],
            brand: $brand,
            categories: (new CategoryModel(Database::getInstance()))->getCategoriesByProductId($prodId),
            tags: (new TagModel(Database::getInstance()))->getTagsByProductId($prodId),
            isPublished: (bool) $prodArr['is_published'],
            images: $this->getImages($prodId),
            quantity: (int) $prodArr['quantity'],
            shipping: $shipping,
            // condition: Condition::ucfirst($prodArr['prod_condition'])
        );
        return $product;
    }

    private function getUnionStr(): string{
        return " UNION ALL ";
    }
}