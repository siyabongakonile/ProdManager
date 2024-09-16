<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Models\Products;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Category;
use App\Models\CategoryModel;
use App\Database;

class ProductsController extends BaseController{
    private ProductModel $model;

    public function __construct(){
        parent::__construct();
        $this->model = new ProductModel(Database::getInstance());
    }

    public function getProducts(Request $request, Response $response){
        $route = $request->getUri();
        $pageNum = 1;
        $limit = SOFTWARE_ITEM_LIMIT;
        $products = [];

        if($request->getSecondUriPart() !== null){
            $pageNum = $this->getPageOrLimitNum($request->getSecondUriPart());
            $pageNum = ($pageNum == false)? 1: $pageNum;
        }

        if($request->getPageNumberFromUri() !== null){
            $limit = $this->getPageOrLimitNum($request->getSecondUriPart());
            $limit = ($limit == false)? SOFTWARE_ITEM_LIMIT: $limit;
        }

        if(count($_GET) == 0)
            $products = $this->getUnfilteredProducts($pageNum, $limit);
        else
            $products = $this->getFilteredProducts($pageNum, $limit);

        $this->render(
            'products/products', 
            [
                'route' => $route,
                'products' => $products,
                'categories' => (new CategoryModel(Database::getInstance()))->getAllCategories(),
                'pageNum' => $pageNum, 
                'limit' => $limit,

            ]
        );
    }

    protected function getUnfilteredProducts(int $pageNum, int $limit): Products{
        return $this->model->getAllProducts($pageNum, $limit);
    }

    protected function getFilteredProducts(int $pageNum, int $limit): Products{
        $code       = $this->getProductsFilterString('code');
        $brands     = $this->getProductsFilterIds('brands');
        $categories = $this->getProductsFilterIds('categories');
        $tags       = $this->getProductsFilterIds('tags');
        $isInstock  = $this->getProductsFilterBool('isinstock');
        $isPublished = $this->getProductsFilterBool('ispublished');

        return $this->model->getFilteredProducts(
            code: $code,
            brands: $brands,
            categories: $categories,
            tags: $tags,
            isInstock: $isInstock,
            isPublished: $isPublished,
            pageNum: $pageNum,
            limit: $limit
        );
    }

    protected function getProductsFilterString(string $key): string|null{
        if(\array_key_exists($key, $_GET) && $_GET[$key] != "")
            return $_GET[$key];
        return null;
    }

    protected function getProductsFilterIds(string $key): array|null{
        if(\array_key_exists($key, $_GET) && $_GET[$key] != "")
            return $this->getIdsFromStr($_GET[$key]);
        return null;
    }

    protected function getProductsFilterBool(string $key): bool|null{
        $value = null;
        if(\array_key_exists($key, $_GET) && $_GET[$key] != ""){
            $strValue = \trim(\strtolower($_GET[$key]));
            if($strValue == "true")
                $value = true;
            if($strValue == "false")
                $value = false;
            return $value;
        }
        return $value;
    }

    /**
     * Takes a string of comma-separated string and 
     * tries to get integers that are less than one.
     * 
     * @param string $str The comma-separeted string
     * @return array of all the given ids that are less than 1
     */
    protected function getIdsFromStr(string $str): array{
        $ids = [];
        $strIds = \explode(',', $str);

        foreach($strIds as $strId){
            $strId = \trim($strId);
            try{
                $tempId = (int) $strId;
                if($tempId == 0) continue;
                $ids[] = $tempId;
            } catch(\Error){
                continue;
            }
        }
        return $ids;
    }
}