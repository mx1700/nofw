# nofw

## 快速开始
1. clone 代码
2. 执行 composer install
1. .evn.example 文件重命名为 .env
3. 浏览器访问 http://127.0.0.1:8000

## TODO
#### 已确认
1. 异常日志记录(中间件实现)
1. 访问日志(中间件实现)
1. console 支持(Symfony/Console)
1. 清除系统缓存功能(确认是否可以通过重启php解决)
1. 根据环境更改日志等级
1. debug 输出（有问题，不好实现）
1. 自定义路由（当路由不匹配时，进入自定义路由规则）
1. 错误页（404,500，中间件实现）

#### 待确认
1. 容器缓存自动更新
1. 获取全局 app ic 的函数
1. 注解缓存自动检测(不支持，需要改DI源码)
1. 根据环境更改日志等级


#### 已完成
1. 中间件

## 使用的开源组件

    "php-di/php-di": "^5.4",                依赖注入容器
    "nikic/fast-route": "^1.2",             路由
    "doctrine/annotations": "^1.3",         注解
    "doctrine/cache": "^1.6",               缓存
    "monolog/monolog": "^1.20",             日志
    "zendframework/zend-diactoros": "^1.3", psr7 支持
    "relay/relay": "~1.0"                   中间件服务

