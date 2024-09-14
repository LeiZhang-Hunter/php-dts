<?php
ini_set('display_errors', 'on');

define('BASE_PATH', __DIR__);


spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    var_dump($class);

    include_once BASE_PATH . '/' . $class . '.php';
});
swoole_async_set([
'enable_coroutine' => false
]);
$config = new Config\Config();
$manager = new Process\SwooleProcessManager([]);
$manager->manager($config);
