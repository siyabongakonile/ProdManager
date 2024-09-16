<?php
declare(strict_types = 1);

namespace Tests\Unit;

use App\Exceptions\RouteNotFoundException;
use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase{
    private Router $router;

    protected function setUp(): void{
        $this->router = new Router();
    }

    /** @test */
    public function itRegistersARoute(){
        $this->router->register('/user', 'get', 'TestUser', 'index');

        $expected = [
            'get' => [
                '/user' => ['TestUser', 'index']
            ]
        ];

        $this->assertEquals($expected, $this->router->getRoutes());
    }

    /** @test */
    public function itRegistersPostToute(){
        $this->router->post('/user', 'TestUser', 'newUser');

        $expected = [
            'post' => [
                '/user' => [
                    'TestUser', 'newUser'
                ]
            ]
        ];

        $this->assertEquals($expected, $this->router->getRoutes());
    }

    /** @test */
    public function itRegistersGetToute(){
        $this->router->get('/user', 'TestUser', 'newUser');

        $expected = [
            'get' => [
                '/user' => [
                    'TestUser', 'newUser'
                ]
            ]
        ];

        $this->assertEquals($expected, $this->router->getRoutes());
    }

    /** @test */
    public function emptyRouterWhenCreated(){
        $router = new Router();

        $this->assertEmpty($router->getRoutes());
    }

    /** 
     * @test 
     * @dataProvider raiseRouteNotFoundProvider
     * */
    public function raiseRouteNotFound(string $route, string $method){
        $users = new class{
            public function newUser(){
                return true;
            }
        };
        echo $users::class;
        $this->router->get('/user', $users::class, 'User');
        $this->router->post('/user', 'user', 'newUser');

        $this->expectException(RouteNotFoundException::class);
        $this->router->resolve($route, $method);
    }

    private function raiseRouteNotFoundProvider(){
        return [
            ['/wrong', 'get'],
            ['/user', 'put'],
            ['/user', 'post'],
            ['/user', 'get']
        ];
    }

    /**
     * @test
     * @dataProvider removeTrailingSlashesProvider
     */
    public function itRemovesTheTrailingSlashes(string $str, string $expected){
        $reflection = new \ReflectionClass($this->router);
        $method = $reflection->getMethod('removeTrailingSlashes');
        $method->setAccessible(true);
        $res = $method->invoke($this->router, $str);

        $this->assertEquals($expected, $res);
    }

    private function removeTrailingSlashesProvider(): array{
        return [
            ['noslashes', 'noslashes'],
            ['test/', 'test'],
            ['test/this/', 'test/this'],
            ['test/this', 'test/this'],
            ['/test/this/', '/test/this'],
        ];
    }

    /** @test */
    public function getEmptyRoutes(){
        $this->assertEmpty($this->router->getRoutes());
    }

    /** @test */
    public function getValidAddedRoutes(){
        $this->router->post('/test', 'test', 'test');

        $expected = [
            'post' => [
                '/test' => ['test', 'test']
            ]
        ];
        $this->assertEquals($expected, $this->router->getRoutes());
    }
}