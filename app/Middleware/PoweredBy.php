<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/10
 * Time: 17:45
 */

namespace App\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PoweredBy
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $next($request, $response);
        $response = $response->withAddedHeader("X-Powered-By", 'nofw');
        return $response;
    }
}