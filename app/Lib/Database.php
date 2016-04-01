<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 16/3/30
 * Time: 16:11
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
        /**
         * D0 D1 D2 有顺序依赖关系，容器会自动注入相关的依赖
         */

        //var_dump($host, $port);
    }
}