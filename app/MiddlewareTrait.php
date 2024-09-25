<?php
declare(strict_types = 1);

namespace App;

use App\Exceptions\AppException;
use App\Middleware\MiddlewareInterface;
use App\Utils;

trait MiddlewareTrait{
    public function middleware(MiddlewareInterface|string $middleware){
        if($middleware instanceof MiddlewareInterface)
            $this->middleware[] = $middleware;
        
        $middlewareObj = $this->getMiddlewareUsingName($middleware);
        if(!$middlewareObj)
            throw new AppException("Middleware with name '{$middleware}' does not exist.");
        $this->middleware[] = $middlewareObj;
        return $this;
    }

    public function getMiddlewareUsingName(string $middlewareName): MiddlewareInterface|false{
        $middlewareDir = __DIR__ . DIR_SEP . "Middleware";
        $files = Utils::getFilesFromDir($middlewareDir);
        $middlewareFilename = $middlewareName . "Middleware.php";
        $middlewareObj = false;
        foreach($files as $file){
            if($file == $middlewareFilename){
                $class = "\\App\\Middleware\\" . $middlewareName . "Middleware";
                if(!\class_exists($class))
                    break;
                $middlewareObj = new $class();
            }
        }
        
        return $middlewareObj;
    }
}