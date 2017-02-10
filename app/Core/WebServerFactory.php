<?php
/**
 * Created by PhpStorm.
 * User: x1
 * Date: 17/2/9
 * Time: 0:03
 */

namespace app\Core;


use DI\Container;
use Dotenv\Exception\ValidationException;
use Relay\RelayBuilder;
use \Zend\Diactoros\Server;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class WebServerFactory
 * @package app\Core
 */
class WebServerFactory
{
    public function create(Container $c)
    {
        $debug = $c->get('app.debug');

        $server = Server::createServer(
            function (Request $request,Response $response, $done) use ($c) {
                $middlewareConf = $c->get('middlewares');
                $middlewares = array_map(function($m) use($c) {
                    if (is_string($m)) {
                        $m = $c->get($m);
                    }
                    if (is_callable($m)) {
                        return $m;
                    } else {
                        throw new ValidationException("错误的中间件类型: " . print_r($m, true));
                    }
                }, $middlewareConf);

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