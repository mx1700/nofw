<?php
/**
 * 项目配置文件，配置项比 env 配置更全面（env 只应该包含环境相关配置）
 * 会加载 env 里的配置，如果 env 里没有，则使用默认值
 */
return [
    'app.env' => env('APP_ENV', 'production'),
    'app.debug' => env('APP_DEBUG', false),
    'app.cache_path' => __DIR__ . '/../storage/cache',
    'app.log_path' => __DIR__ . '/../storage/log',
];