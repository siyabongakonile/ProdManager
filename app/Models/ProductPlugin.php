<?php 
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

/**
 * The class that works with the accosiations of products to plugins.
 * 
 * It keeps track of plugins of which products are added to.
 */
class ProductPlugin extends BaseModel{
    public function addPluginNameToProduct(int $productId, string $pluginName){
        $sql = "INSERT INTO product_plugin VALUES ({$productId}, '{$pluginName}')";
        $this->database->query($sql);
    }

    public function getPluginNamesByProductId(int $productId): array{
        $plugins = [];
        $sql = "SELECT * FROM product_plugin WHERE prod_id = {$productId}";

        $res = $this->database->query($sql);
        if($res->num_rows < 1)
            return [];
        
        while($row = $res->fetch_assoc()){
            $plugins[] = $row['plugin_sys_name'];
        }
        return $plugins;
    }

    public function removeRowWithPluginName(string $pluginSystemName){
        $sql = "DELETE FROM product_plugin WHERE plugin_sys_name = '{$pluginSystemName}'";
        $this->database->query($sql);
    }
}