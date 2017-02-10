<?php
/**
 * 假设是一个数据库类，为了演示依赖注入
 */

namespace App\Lib;


class Database
{
    /**
     * 从构造函数注入配置的示例
     * @param $host
     * @param $port
     * @Inject({"db.host", "db.port"})
     */
    public function __construct($host, $port)
    {
        //var_dump($host, $port);
    }
}