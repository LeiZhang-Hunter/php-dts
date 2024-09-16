<?php
namespace Source\Canal;

class Config {
    public $ip;

    public $port;

    public $clientId;

    public $destination;

    public $filter = ".*\..*";

    public $readerConfig;

    public function __construct() {
        $this->readerConfig = new ReaderConfig();
    }

    public function load($config) {
        if (isset($config["ip"])) {
            $this->ip = $config["ip"];
        }

        if (isset($config["port"])) {
            $this->port = $config["port"];
        }

        if (isset($config["clientId"])) {
            $this->clientId = $config["clientId"];
        }

        if (isset($config["destination"])) {
            $this->destination = $config["destination"];
        }

        if (isset($config["filter"])) {
            $this->filter = $config["filter"];
        }

        if (isset($config["reader"])) {
            $this->readerConfig->init($config["reader"]);
        }
    }
}
