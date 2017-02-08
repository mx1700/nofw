<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/8
 * Time: 19:06
 */

namespace App\Core;

use \DI\Container;
use \Zend\Diactoros\Server;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Application
{
    /**
     * 项目根目录
     * @var null|string
     */
    private $basePath = null;

    /**
     * @var Container
     */
    private $container = null;

    /**
     * Application constructor.
     * @param string $basePath  项目跟目录
     */
    public function __construct($basePath) {
        $this->basePath = rtrim($basePath, '\/');;
        // env 配置需要先加载，conf 依赖 env 配置
        $this->loadEnv();
        $this->buildContainer();
    }

    /**
     * 加载环境变量配置，配置文件在项目根目录 .env 文件
     */
    private function loadEnv() {
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = new \Dotenv\Dotenv($this->basePath);
            $dotenv->load();
        }
    }

    /**
     * 构建依赖注入容器
     */
    private function buildContainer() {
        $debug = env('APP_DEBUG', false);

        $builder = new \DI\ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAnnotations(true);

        if (!$debug) {
            /*
             * 生产环境为 DI 容器增加缓存。这里使用的 Yac 作为缓存
             * 尝试过远程 redis 作为缓存，效果不好
             * 文件缓存几乎没有效果，目前看 php 扩展实现的缓存效果最好
             */
            //TODO:需要移到外部进行配置
//            $cache = new \App\Lib\YacCache(new Yac());
//            $builder->setDefinitionCache($cache);
        }

        /**
         * 把配置文件加载到 DI 容器中
         */
        $builder->addDefinitions($this->basePath . '/config/app.php');
        $builder->addDefinitions($this->basePath . '/config/db.php');

        $this->container = $builder->build();
    }

    /**
     * 创建 web server
     * @return Server
     */
    public function createWebServer() {
        $c = $this->container;
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
                    case \FastRoute\Dispatcher::NOT_FOUND:
                        $response->withStatus(404);
                        break;
                    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                        $response->withStatus(404);
                        break;
                    case \FastRoute\Dispatcher::FOUND:
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
        return $server;
    }
}