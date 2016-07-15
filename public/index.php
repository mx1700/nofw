<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('PRC');

/**
 * bootstrap 返回 DI 容器
 * @var Container $app
 */
$app = require __DIR__ . '/../app/application.php';

$server = Zend\Diactoros\Server::createServer(
    function (Request $request,Response $response, $done) use ($app) {
        //var_dump($request);exit;
        /**
         * 从容器获取当前匹配的路由，内容就是 routes 配置里匹配上的项
         */
        $router = $app->get('router');
        $route = $router->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );
        switch ($route[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->withStatus(404);
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->withStatus(404);
                break;
            case FastRoute\Dispatcher::FOUND:
                $controller = $route[1];
                $parameters = $route[2];

                $app->set(Request::class, $request);
                $app->set(Response::class, $response);

                $_get = $request->getQueryParams();
                $_post = $request->getParsedBody();
                $_files = $request->getUploadedFiles();
                $parameters = array_merge($parameters, $_get, $_post, $_files);
                /**
                 * 调用控制器，返回 Response 对象
                 * $app 是 DI 容器对象，call 方法调用 控制器，会为控制器注入所需的依赖
                 */
                $response = $app->call($controller, $parameters);
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

$server->listen();
