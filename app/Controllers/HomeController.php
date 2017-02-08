<?php
namespace App\Controllers;

use App\Lib\Database;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;

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
     * @var Response
     */
    private $response;

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
     * @param Response $response
     * @param Database $db
     * @param LoggerInterface $log
     */
    public function __construct(Request $request, Response $response, Database $db, LoggerInterface $log)
    {
        $this->request = $request;
        $this->response = $response;
        $this->db = $db;
        $this->log = $log;
    }

    public function hello($id = 1, $name = 'Tom')
    {
        $this->log->error("aaa");
        return new TextResponse("hello, {$name}. env: {$this->env}. debug:{$this->debug}, id:{$id}");
    }

    public function getUser($id)
    {
        return new JsonResponse(['id' => $id, 'name' => '你好']);
    }
}