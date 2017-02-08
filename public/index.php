<?php
require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('PRC');

/**
 * App\Core\Application
 */
$app = require __DIR__ . '/../app/app.php';
$server = $app->createWebServer();
$server->listen();
