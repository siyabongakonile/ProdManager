<?php
declare(strict_types = 1);

namespace App\Middleware;

use App\Request;
use App\Response;

class AuthMiddleware implements MiddlewareInterface{
    public function __invoke(Request $request, Response $response, $next){
        if($request->getCookie('session')['auth']){
            return $next($request, $response);
        } else {
            return $response->sendToPage('/login');
        }
    }
}