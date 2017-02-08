<?php
/**
 * 项目配置文件，配置项比 env 配置更全面（env 只应该包含环境相关配置）
 * 会加载 env 里的配置，如果 env 里没有，则使用默认值
 */
use FastRoute\RouteCollector;
use Interop\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return [
    'app.env' => env('APP_ENV', 'production'),
    'app.debug' => env('APP_DEBUG', false),
    'app.cache_path' => dirname(__DIR__) . '/storage/cache',
    'app.log_path' => dirname(__DIR__) . '/storage/log',

    /**
     * 日志类配置
     */
    LoggerInterface::class => function(ContainerInterface $c) {
        $log_path = $c->get('app.log_path');
        $log = new Logger('App');
        $log->pushHandler(new StreamHandler($log_path . '/app.log', Logger::NOTICE));
        return $log;
    },
    /**
     * 加载路由配置文件
     */
    'routes' => function() {
        return require dirname(__DIR__) . '/app/routes.php';
    },
    /**
     * 路由管理类
     */
    'router' => function(ContainerInterface $c) {   //路由控制器
        $routes = $c->get('routes');
        $cache_path = $c->get('app.cache_path');
        $debug = $c->get('app.debug');
        $dispatcher = FastRoute\cachedDispatcher(
            function (RouteCollector $r) use ($routes) {
                foreach ($routes as $routeDef) {
                    $r->addRoute($routeDef[0], $routeDef[1], $routeDef[2]);
                }
            }, [
            'cacheFile' => $cache_path . '/route.cache',
            'cacheDisabled' => $debug,
        ]);
        return $dispatcher;
    },
];