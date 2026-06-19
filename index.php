<?php

use app\core\Container;
use app\core\Router;
use app\core\App;

require_once __DIR__ . '/composer/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

App::init(new Container());
App::container()->setVariable('db_config', [
    'dbname' => $_ENV['DB_NAME'],
    'pass'   => $_ENV['DB_PASS'],
    'host'   => $_ENV['DB_HOST'],
    'user'   => $_ENV['DB_USER'],
]);

Router::dispatchInit();
