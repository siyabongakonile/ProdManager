<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Request;
use App\Response;

use App\Exceptions\AppException;
use App\Exceptions\NotImplementedException;
use App\Exceptions\ControllerExceptions\InvalidArgException;
use App\Exceptions\ControllerExceptions\InvalidURLException;

class BrandsController extends BaseController{
    public function getBrands(Request $request, Response $response){
        $page = 1;
        $limit = 10;

        try{
            if($request->getSecondUriPart() !== null)
                $page = $this->getBrandsArgNum($request->getSecondUriPart());
            
            if($request->getPageNumberFromUri() !== null)
                $limit = $this->getBrandsArgNum($request->getPageNumberFromUri());
        } catch(AppException){
            throw new InvalidURLException();
        }

        $this->render('brands/brands');
    }

    public function getBrand(Request $request, Response $response){
        throw new NotImplementedException();
    }

    public function getCreateBrand(Request $request, Response $response){
        throw new NotImplementedException();
    }

    public function createBrand(Request $request, Response $response){
        throw new NotImplementedException();
    }

    public function updateBrand(Request $request, Response $response){
        throw new NotImplementedException();
    }

    public function deleteBrand(Request $request, Response $response){
        throw new NotImplementedException();
    }

    private function getBrandsArgNum(string $argNum): int{
        $argNum = (int) $argNum;
        if($argNum < 1)
            throw new InvalidArgException();
        return $argNum;
    }
}