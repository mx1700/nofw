<?php
/**
 * Created by PhpStorm.
 * User: x1
 * Date: 17/2/9
 * Time: 0:03
 */

namespace app\Core;


use DI\Container;
use Relay\RelayBuilder;
use \Zend\Diactoros\Server;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class WebServerFactory
{
    public function create(Container $c) {
        $debug = $c->get('app.debug');

        $server = Server::createServer(
            function (Request $request,Response $response, $done) use ($c, $debug) {
                $middlewares = $c->get('middlewares');
                $relayBuilder = new RelayBuilder();
                $relay = $relayBuilder->newInstance($middlewares);
                $response = $relay($request, $response);
                return $response;
            },
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );

        //TODO:是否应该使用中间件代替？
        if ($debug && class_exists('Symfony\Component\Debug\Debug')) {
            \Symfony\Component\Debug\Debug::enable();
            \Symfony\Component\Debug\ErrorHandler::register();
            \Symfony\Component\Debug\ExceptionHandler::register();
            \Symfony\Component\Debug\DebugClassLoader::enable();
        }
        return $server;
    }
}