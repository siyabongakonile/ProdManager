<?php
declare(strict_types = 1);

namespace App;

use App\Route;

use App\Controllers\ErrorController;
use App\Exceptions\RouteNotFoundException;
use App\Middleware\MiddlewareInterface;
use App\MiddlewareTrait;

class Router{
    /** @var Route $route */
    private array $routes = [];
    private Request $request;
    private Response $response;
    /** @var array<MiddlewareInterface> $middleware */
    private array $middleware = [];

    use MiddlewareTrait;


    public function __construct(Request $request, Response $response){
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Register a route.
     *
     * @param string $route
     * @param string $method
     * @param string $class
     * @param string $action
     * @return Route
     */
    public function register(string $route, string $method, string $class, string $action): Route{
        $route = new Route($method, $route, $class, $action);
        $this->routes[] = $route;
        return $route;
    }

    /**
     * Register a get route.
     *
     * @param string $route
     * @param string $class
     * @param string $action
     * @return Route
     */
    public function get(string $route, string $class, string $action): Route{
        return $this->register($route, 'get', $class, $action);
    }

    /**
     * Register a post route.
     *
     * @param string $route
     * @param string $class
     * @param string $action
     * @return Route
     */
    public function post(string $route, string $class, string $action): Route{
        return $this->register($route, 'post', $class, $action);
    }

    /**
     * Resolve the given route.
     */
    public function resolve(){
        $requestUri = (\explode('?', $this->request->getUri()))[0];
        $requestUri    = \strtolower($requestUri);
        $requestMethod = \strtolower($this->request->getMethod());

        if($requestUri !== '/')
            $requestUri = $this->removeTrailingSlashes($requestUri);

        if($this->hasRoute($requestMethod, $requestUri))
            $this->resolveNonvariableUri($requestUri, $requestMethod);
        else
            $this->resolveVariableUri($requestUri, $requestMethod);
    }

    private function hasRoute(string $method, string $uri): bool{
        return $this->getRoute($method, $uri)? true: false;
    }

    /**
     * Check if the uri contains variable values or not.
     * 
     * You do this by checking if the route is matching 
     * any of the corrent routes exactly.
     *
     * @param string $uri
     * @return boolean
     */
    private function isVariableUri(string $uri): bool{
        $uriParts = \explode("/", $uri);
        foreach($uriParts as $uriPart){
            $startWithBracket = \strpos($uriPart, '{') === 0;
            $endWithBracket = \strpos($uriPart, '}') === \strlen($uriPart) - 1;
            if($startWithBracket and $endWithBracket)
                return true;
        }
        return false;
    }

    /**
     * Get the registed routes.
     *
     * @return array<Route>
     */
    public function getRoutes(): array{
        return $this->routes;
    }

    public function getRoute($method, $uri): ?Route{
        foreach($this->routes as $route){
            if($route->method == $method && $route->route == $uri)
                return $route;
        }
        return null;
    }

    /**
     * Resolve routes that do not have variable values.
     *
     * @param string $uri
     * @param string $method
     * @return void
     */
    private function resolveNonvariableUri(string $uri, string $method){
        $route = $this->getRoute($method, $uri);
        [$class, $action] = $this->getClassAndActionOfRoute($route);

        if(!$this->actionExists($class, $action))
            throw new RouteNotFoundException();

        $this->runMiddleware($route);
        $this->callControllerAction($class, $action);
    }

    /**
     * Get the controller and action method in that controller of a given route.
     * 
     * @param Route $route
     * @return array The Controller class and its action method.
     */
    private function getClassAndActionOfRoute(Route $route): array{
        $class = $route->controller;
        $action = $route->action;
        return [$class, $action];
    }

    /**
     * Resolve routes that have variable values.
     *
     * @param string $uri
     * @param string $method
     * @return void
     */
    private function resolveVariableUri(string $uri, string $method){
        $matchingRoute = $this->getMatchingVariableRoute($uri, $method);
        if($matchingRoute === false)
            throw new RouteNotFoundException();

        $route = $this->getRoute($method, $matchingRoute);
        [$class, $action] = $this->getClassAndActionOfRoute($route);
        if(!$this->actionExists($class, $action))
            throw new RouteNotFoundException();

        $this->runMiddleware($route);
        $this->callControllerAction($class, $action);
    }

    private function callControllerAction(string $class, string $action){
        $class = new $class();
        $class->$action($this->request, $this->response);
    }

    /**
     * Check if the given class and method exists.
     *
     * @param string $class
     * @param string $method
     * @return boolean
     */
    private function actionExists(string $class, string $method): bool{
        return \class_exists($class) && \method_exists(new $class(), $method);
    }

    private function getMatchingVariableRoute(string $uri, string $method): string|bool{
        $uriParts = \explode('/', $uri);

        /** @var Route $route */
        foreach($this->routes as $route){
            $routeParts = \explode('/', $route->route);
            if($route->method == $method && \count($routeParts) == \count($uriParts) 
                && $uriParts[1] === $routeParts[1] && $this->isVariableUri($route->route)){
                return $route->route;
            }
        }
        return false;
    }

    private function removeTrailingSlashes(string $uri): string{
        if($uri[-1] == '/')
            return \substr($uri, 0, \strlen($uri) - 1);
        return $uri;
    }

    private function runMiddleware(Route $route){
        $next = function(){};

        $allMiddleware = array_merge($this->middleware, $route->getMiddleware());
        $revAllMiddleware = array_reverse($allMiddleware);
        foreach($revAllMiddleware as $middleware){
            $next = function() use ($middleware, $next){
                return $middleware->handle($this->request, $this->response, $next);
            };
        }

        return $next();
    }
}