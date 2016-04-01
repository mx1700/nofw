此项目是参考 nofw 项目写的一个示例，里边加了一些中文注释。

nofw 是一个在不用框架情况下，创建项目骨架的示例。
其核心是一个依赖注入容器，对象生命周期、依赖关系和配置全部通过依赖注入容器来管理。（有点像Spring）

整个示例关键代码文件只有三个：
public/index.php		单一入口文件
app/bootstrap.php		引导文件
app/routes.php		路由配置

依赖的开源组件：
php-di/php-di		依赖注入容器，项目的核心
symfony/http-foundation	req 和 resp 的封装
nikic/fast-route		路由
vlucas/phpdotenv		环境配置读取
doctrine/annotations	注解支持
doctrine/cache		缓存
symfony/debug		debug
monolog/monolog		日志

依赖注入的优点：
1.组件之前耦合度低
2.组件不依赖骨架（或者叫框架）代码，可移植性好
3.测试友好
4.自动管理对象生命周期
5.可以通过容器实现AOP技术（示例代码里没有涉及这块）



nofw 项目地址
 https://github.com/Swader/nofw