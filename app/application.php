<?php
/**
 * 引导文件不做任何业务处理，仅仅是加载配置，包括环境配置，项目配置，注入配置等
 */

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

/*
 * 加载环境变量配置，配置文件在项目根目录 .env 文件
 */
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

/**
 * 加载环境变量包装函数，提供了返回默认值功能
 * @param $name
 * @param null $default
 * @return bool|null|string
 */
function env($name, $default = null)
{
    $val = getenv($name);
    if ($val !== false) {
        if ($val === 'true') return true;
        if ($val === 'false') return false;
        return $val;
    }
    return $default;
}

$debug = env('APP_DEBUG', false);


//TODO:应该在 c 层加载，console 模式不需要
if ($debug) {
    //debug模式启用 Symfony\Debug 组件，提供友好的报错页面
    Debug::enable();
    ErrorHandler::register();
    ExceptionHandler::register();
}

/**
 * 创建 DI 容器
 */
$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(true);

if (!$debug) {
    /*
     * 生产环境为 DI 容器增加缓存。这里使用的 Yac 作为缓存
     * 尝试过远程 redis 作为缓存，效果不好
     * 文件缓存几乎没有效果，目前看 php 扩展实现的缓存效果最好
     */
    $cache = new App\Lib\YacCache(new Yac());
    //$cache = new App\Lib\PhpFileCache(__DIR__ . '/../storage/cache');
    $builder->setDefinitionCache($cache);

}

/**
 * 把配置文件加载到 DI 容器中
 */
$builder->addDefinitions(__DIR__ . '/../config/app.php');
$builder->addDefinitions(__DIR__ . '/../config/database.php');

$container = $builder->build();
return $container;
