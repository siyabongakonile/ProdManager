<?php
declare(strict_types = 1);

namespace App\Middleware;

interface MiddlewareInterface{
    public function __invoke($request, $response, $next);
}