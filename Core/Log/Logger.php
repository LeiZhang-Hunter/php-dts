<?php

namespace Core\Log;

use Monolog\Handler\StreamHandler;

class Logger {
    private static $logger = null;
    public static function getLogger() {
        if (self::$logger == null) {
            self::$logger = new \Monolog\Logger("dts");
            self::$logger->pushHandler(new StreamHandler('php://stdout', \Monolog\Logger::INFO));
        }
        return self::$logger;
    }
}
