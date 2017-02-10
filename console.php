<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__ . './app/app.php';
$server = $app->createConsoleServer();
$server->run();