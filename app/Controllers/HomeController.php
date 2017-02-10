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
     * @Inject
     */
    private $request;

    /**
     * @var Response
     * @Inject
     */
    private $response;

    /**
     * @var Database
     * @Inject
     */
    private $db;

    /**
     * @var LoggerInterface
     * @Inject
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


    public function hello($id = 1, $name = 'Tom')
    {
        $debug = $this->debug ? 'true' : 'false';
        $this->log->info("id: ${id}");  //记录日志
        return new TextResponse("hello, {$name}. env: {$this->env}. debug:{$debug}. method: " . $this->request->getMethod());
    }

    public function getUser($id)
    {
        return new JsonResponse(['id' => $id, 'name' => '你好']);
    }

}