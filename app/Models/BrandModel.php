<?php
declare(strict_types = 1);

namespace App\Models;

use App\Database;

class BrandModel extends BaseModel{
    public function __construct(Database $db){
        parent::__construct($db);
    }

    /**
     * Get all the brands added to the database.
     * 
     * @return array Returns a list of brand objects
     */
    public function getAllBrands(): array{
        $sql = "SELECT * FROM brand";
        $brands = [];

        $result = $this->database->query($sql);
        while($row = $result->fetch_assoc()){
            $brands[] = $this->convertDBBrandToBrandObj($row);
        }
        return $brands;
    }

    /**
     * Get a brand object using its id.
     * 
     * @param int $brandId
     * @return Brand|bool Returns the brand object or false otherwise
     */
    public function getBrand(int $brandId): Brand|bool{
        $sql = "SELECT * FROM brand WHERE brand_id = {$brandId}";
        if($res = $this->database->query($sql)){
            if($res->num_rows == 0) return false;
            return $this->convertDBBrandToBrandObj($res->fetch_assoc());
        }
        return false;
    }

    /**
     * Add brand to the database.
     * 
     * @param string $name The brand name
     * @return Brand The newly created brand
     */
    public function addBrand(string $name): Brand|bool{
        $sql = "INSERT INTO brand (brand_name) VALUES ('$name')";
        if($this->database->query($sql)){
            return new Brand((int) $this->database->getInsertId(), $name);
        }
        return false;
    }

    /**
     * Delete a brand from the database.
     * 
     * @param int $brandId
     * @return bool Returns true if the brand was deleted of false otherwise
     */
    public function deleteBrand(int $brandId): bool{
        $sql = "DELETE FROM brand WHERE brand_id = {$brandId}";
        return $this->database->query($sql);
    }

    /**
     * Update an existing brand.
     * 
     * @param int $brandId
     * @param string $brandName
     * @return bool Returns true if the brand was modified or false otherwise
     */
    public function updateBrand(int $id, string $name): bool{
        $sql = "UPDATE brand SET brand_name = '{$name}' WHERE brand_id = {$id}";
        return $this->database->query($sql);
    }

    protected function convertDBBrandToBrandObj(array $dbBrand): Brand{
        return new Brand(
            id: (int) $dbBrand['brand_id'],
            name: $dbBrand['brand_name']
        );
    }
}