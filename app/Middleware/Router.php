<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/10
 * Time: 13:41
 */

namespace App\Middleware;


use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 路由中间件
 * @package App\Middleware
 */
class Router
{
    public function __construct(Container $c, $routes, $cache_path, $debug = false)
    {
        $this->c = $c;
        $this->routes = $routes;
        $this->cachePath = $cache_path;
        $this->debug = $debug;
    }

    /**
     * 获取路由分发器
     */
    private function getRouterDispatcher()
    {
        $routes = $this->routes;
        $cache_path = $this->cachePath;
        $debug = $this->debug;
        $dispatcher = \FastRoute\cachedDispatcher(
            function (RouteCollector $r) use ($routes, $cache_path, $debug) {
                foreach ($routes as $routeDef) {
                    $r->addRoute($routeDef[0], $routeDef[1], $routeDef[2]);
                }
            }, [
            'cacheFile' => $cache_path,
            'cacheDisabled' => $debug,
        ]);
        return $dispatcher;
    }
    /**
     * Execute the middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $router = $this->getRouterDispatcher();
        $route = $router->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );
        switch ($route[0]) {
            case Dispatcher::NOT_FOUND:
                $response = $response->withStatus(404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = $response->withStatus(405);
                break;
            case Dispatcher::FOUND:
                $controller = $route[1];
                $parameters = $route[2];

                //容器中不应该注入 req & res，因为在不同中间件中，两个对象会发生变化，而容器里的对象不会发生变化
                //目前没有想到更好的办法把两个值注入到控制器内
                $this->c->set(Request::class, $request);
                $this->c->set(Response::class, $response);

                $_get = $request->getQueryParams();
                $_post = $request->getParsedBody();
                $_files = $request->getUploadedFiles();
                $parameters = array_merge($parameters, $_get, $_post, $_files);

                /**
                 * 调用控制器，返回 Response 对象
                 * 容器对象 call 方法调用 控制器，会为控制器注入所需的依赖
                 */
                $response = $this->c->call($controller, $parameters);
                break;
        }
        $response = $next($request, $response);
        return $response;
    }
}