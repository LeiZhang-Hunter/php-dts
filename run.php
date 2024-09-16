<?php
ini_set('display_errors', 'on');

define('BASE_PATH', __DIR__);


spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    var_dump(BASE_PATH . '/' . $class . '.php');

    include_once BASE_PATH . '/' . $class . '.php';
});

include_once "vendor/autoload.php";
swoole_async_set([
    'enable_coroutine' => false
]);
$config = new Config\Config();
$manager = new Core\Process\SwooleProcessManager($config->getConfig());
$manager->manager($config);
