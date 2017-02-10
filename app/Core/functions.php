<?php
/**
 * Created by PhpStorm.
 * User: lizhaoguang
 * Date: 17/2/8
 * Time: 19:15
 */

if (!function_exists('env')) {
    /**
     * 加载环境变量包装函数
     * @param $name
     * @param null $default 默认值
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
}

if (!function_exists('app')) {
    /**
     * 获取容器内实体
     * @param null $name
     * @return \App\Core\Application|mixed
     */
    function app($name = null) {
        if (!$name) {
            return \App\Core\Application::getInstance();
        } else {
            return \App\Core\Application::getInstance()->get($name);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * 调试输出并终止程序
     * @param  mixed
     */
    function dd() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (function_exists('dump')) {
                dump($arg);
            } else {
                var_dump($arg);
            }
        }
        die(1);
    }
}
