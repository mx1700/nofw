<?php
use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
date_default_timezone_set('PRC');

/**
 * bootstrap 返回 DI 容器
 * @var Container $app
 */
$app = require __DIR__ . '/../app/bootstrap.php';

/**
 * 从容器获取当前匹配的路由，内容就是 routes 配置里匹配上的项
 */
$route = $app->get('route');

/**
 * @var $response Response
 */
$response = null;

switch ($route[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response("页面不存在", 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response("页面不存在", 404);
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $route[1];
        $parameters = $route[2];
        /**
         * @var Request $req;
         */
        $req = $app->get(Request::class);
        $_get = $req->query->all();
        $_post = $req->request->all();
        $_file = $req->files->all();
        $parameters = array_merge($parameters, $_get, $_post, $_file);
        /**
         * 调用控制器，返回 Response 对象
         * $app 是 DI 容器对象，call 方法调用 控制器，会为控制器注入所需的依赖
         */
        $response = $app->call($controller, $parameters);
        break;
}
/**
 * 输出内容
 */
$response->send();
