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
