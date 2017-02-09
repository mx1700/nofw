<?php
/**
 * Created by PhpStorm.
 * User: x1
 * Date: 17/2/9
 * Time: 0:03
 */

namespace app\Core;


use DI\Container;
use FastRoute\Dispatcher;
use \Zend\Diactoros\Server;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class WebServerFactory
{
    public function create(Container $c) {
        $server = Server::createServer(
            function (Request $request,Response $response, $done) use ($c) {
                /**
                 * 从容器获取当前匹配的路由，内容就是 routes 配置里匹配上的项
                 */
                $router = $c->get('router');
                $route = $router->dispatch(
                    $request->getMethod(),
                    $request->getUri()->getPath()
                );
                switch ($route[0]) {
                    case Dispatcher::NOT_FOUND:
                        $response->withStatus(404);
                        break;
                    case Dispatcher::METHOD_NOT_ALLOWED:
                        $response->withStatus(404);
                        break;
                    case Dispatcher::FOUND:
                        $controller = $route[1];
                        $parameters = $route[2];

                        $c->set(Request::class, $request);
                        $c->set(Response::class, $response);

                        $_get = $request->getQueryParams();
                        $_post = $request->getParsedBody();
                        $_files = $request->getUploadedFiles();
                        $parameters = array_merge($parameters, $_get, $_post, $_files);
                        /**
                         * 调用控制器，返回 Response 对象
                         * $app 是 DI 容器对象，call 方法调用 控制器，会为控制器注入所需的依赖
                         */
                        $response = $c->call($controller, $parameters);
                        break;
                }
                return $response;
            },
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $debug = $c->get('app.debug');
        if ($debug && class_exists('Symfony\Component\Debug\Debug')) {
            \Symfony\Component\Debug\Debug::enable();
            \Symfony\Component\Debug\ErrorHandler::register();
            \Symfony\Component\Debug\ExceptionHandler::register();
            \Symfony\Component\Debug\DebugClassLoader::enable();
        }
        return $server;
    }
}