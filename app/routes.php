<?php
/**
 * 路由配置文件
 * 每一项对应一个路由
 */
return [
    /**
     * 一个路由配置由三项组成：
     * 1.method(GET|POST|PUSH|DELETE)
     * 2.路径(支持参数格式校验)
     * 3.对应controller和action
     */
    ['GET', '/', ['App\Controllers\HomeController', 'hello']],
    ['GET', '/user/{id:\d+}', ['App\Controllers\HomeController', 'getUser']],
    ['GET', '/hello', ['App\Controllers\HelloController', 'index']],
];