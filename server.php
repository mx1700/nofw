<?php
/**
 * 使用php内置 web server 运行项目的引导文件
 * 你可以不安装 nginx/apache ，在项目根目录下运行 composer serve ，然后访问 localhost:9000
 * 仅供开发使用
 */
if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css)$/', $_SERVER["REQUEST_URI"]))
    return false;    // 直接返回请求的文件
else {
    require 'public/index.php';
}