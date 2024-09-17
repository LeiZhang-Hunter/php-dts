<?php
ini_set('display_errors', 'on');

define('BASE_PATH', __DIR__);


spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);

    include_once BASE_PATH . '/' . $class . '.php';
});

include_once "vendor/autoload.php";
swoole_async_set([
    'enable_coroutine' => false
]);
//Core\Log\Logger::getLogger();
$config = new Config\Config();
$manager = new Core\Process\SwooleProcessManager($config->getConfig());
$manager->manager($config);
