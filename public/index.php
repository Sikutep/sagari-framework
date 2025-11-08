<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

use Core\Application;

$app = new Application(BASE_PATH);

$app->boot();

$app->handleRequest();