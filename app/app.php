<?php
/**
 * 引导文件不做任何业务处理，仅仅是加载配置，包括环境配置，项目配置，注入配置等
 */

$app = new \App\Core\Application(dirname(__DIR__));
return $app;
