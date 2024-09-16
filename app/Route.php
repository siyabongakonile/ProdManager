<?php
declare(strict_types = 1);

namespace App;

use App\Controllers\BaseController;
use App\Middleware\MiddlewareInterface;

class Route{
    /** @var array<MiddlewareInterface> $middlewarePipeline All the routes middleware. */
    public array $middlewarePipeline = [];

    /** @var string $method Request method for the route. */
    public string $method;

    /** @var string $route Route to go to. */
    public string $route;

    /** @var string $controller Controller class for the route. */
    public string $controller;

    /** @var string $action Controller action */
    public string $action; 

    public function __construct(string $method, string $route, string $controller, string $action){
        $this->method = $method;
        $this->route  = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    /** 
     * Add middleware class to the pipeline. 
     * 
     * @var string $middleware The class name of the middleware.
     * @return MiddlewareInterface Current object
     * */
    public function middleware(MiddlewareInterface $middleware): static{
        $this->middlewarePipeline[] = $middleware;
        return $this;
    }

    public static function execMiddleware(Route $route){
        
    }
}