<?php
namespace App\Controllers;

use App\Lib\Database;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 控制器示例
 * 演示了 构造函数依赖注入 和 属性依赖注入
 * Class HomeController
 * @package App\Controllers
 */
class HomeController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * 参数注入示例
     * @Inject("app.env")
     * @var string env
     */
    private $env;

    /**
     * 参数注入示例
     * @Inject("app.debug")
     * @var string debug
     */
    private $debug;

    /**
     * 构造函数，依赖会被 DI 容器自动注入
     * HomeController constructor.
     * @param Request $request
     * @param Database $db
     * @param LoggerInterface $log
     */
    public function __construct(Request $request, Database $db = null, LoggerInterface $log = null)
    {
        $this->request = $request;
        $this->db = $db;
        $this->log = $log;
    }

    public function hello($id = 1)
    {
        $name = $this->request->get('name', 'foo');
        return new Response("hello, {$name}. env: {$this->env}. debug:{$this->debug}, id:{$id}");
    }

    public function getUser($id)
    {
        return new JsonResponse(['id' => $id, 'name' => '你好']);
    }

    /**
     * $log 会自动注入到方法里
     * @param LoggerInterface $log
     * @param string $name
     * @param int $age
     * @return Response
     */
    public function test(LoggerInterface $log, $name = 'zhang', $age = 0)
    {
        $log->info('test');
        var_dump($log);
        return new Response("hello {$name}, age: {$age}");
    }
}