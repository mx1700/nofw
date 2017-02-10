<?php
/**
 * 引导文件不做任何业务处理，仅仅是加载配置，包括环境配置，项目配置，注入配置等
 */

namespace App\Core;

use \DI\Container;
use \Dotenv\Exception\ValidationException;
use \Zend\Diactoros\Server;
use Interop\Container\ContainerInterface;

class Application implements ContainerInterface
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
     * 全局实例对象
     * @var static
     */
    private static $instance;

    /**
     * Application constructor.
     * @param string $basePath  项目跟目录
     */
    public function __construct($basePath) {
        if (static::$instance) {
            throw new ValidationException("只能存在一个 Application 实例");
        }
        $this->basePath = rtrim($basePath, '\/');;
        // env 配置需要先加载，conf 依赖 env 配置
        $this->loadEnv();
        $this->buildContainer();
        static::$instance = $this;
    }

    /**
     * 获取全局实例
     * @return Application
     */
    public static function getInstance() {
        return static::$instance;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed Entry.
     */
    public function get($id) {
        return $this->container->get($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id) {
        return $this->container->has($id);
    }

    /**
     * 加载环境变量配置，配置文件在项目根目录 .env 文件
     */
    private function loadEnv() {
        if (class_exists('\Dotenv\Dotenv')) {
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
            //TODO:检查是否有 yac apcu，有则使用缓存，没有则不使用
            //TODO:需要检验自己实现的 PhpFileCache 是否有效
            //$cache = new \App\Lib\YacCache();
            //$cache = new \Doctrine\Common\Cache\FilesystemCache($this->basePath . '/storage/cache/app/');
            //$builder->setDefinitionCache($cache);
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
        return $this->container->call([WebServerFactory::class, 'create']);
    }

    /**
     * 创建命令行服务
     */
    public function createConsoleServer() {
        return $this->container->call([ConsoleServerFactory::class, 'create']);
    }
}