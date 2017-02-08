<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 16/10/31
 * Time: 18:52
 */

namespace App\Controllers;
use Zend\Diactoros\Response\TextResponse;
use Psr\Log\LoggerInterface;

class HelloController
{
    /**
     * @var LoggerInterface
     * @Inject
     */
    private $logger;

    public function index($id = 1) {
        $this->logger->info("aaa");
        return new TextResponse("hello" . $id);
    }
}