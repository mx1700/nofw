<?php
/**
 * 项目配置文件，配置项比 env 配置更全面（env 只应该包含环境相关配置）
 * 会加载 env 里的配置，如果 env 里没有，则使用默认值
 */
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
        $log->pushHandler(new StreamHandler($log_path . '/error.log', Logger::ERROR));
        $log->pushHandler(new StreamHandler($log_path . '/app.log', Logger::DEBUG));
        return $log;
    },
    /**
     * 加载路由配置文件
     */
    'routes' => function() {
        return require dirname(__DIR__) . '/app/routes.php';
    },
    /**
     * 配置路由管理类
     */
    \App\Middleware\Router::class => function(ContainerInterface $c) {   //路由控制器
        return new \App\Middleware\Router(
            $c,
            $c->get('routes'),
            $c->get('app.cache_path') . '/system/route.cache',
            $c->get('app.debug'));
    },
    //中间件配置
    'middlewares' => [
        \App\Middleware\Router::class,
        \App\Middleware\PoweredBy::class,
    ],
    //命令行功能配置
    'commends' => [
        \App\Console\TestCommand::class
    ]
];