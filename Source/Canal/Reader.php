<?php

namespace Source\Canal;

use xingwenge\canal_php\CanalClient;
use xingwenge\canal_php\CanalConnectorFactory;
use xingwenge\canal_php\Fmt;
use function Swoole\Coroutine\run;
use function Swoole\Coroutine\go;

class Reader {

    /**
     * @var \xingwenge\canal_php\adapter\swoole\CanalConnector
     */
    private $client;

    private $config;

    private $isRun = false;

    public function __construct(Config $config) {
        $this->client = CanalConnectorFactory::createClient(CanalClient::TYPE_SWOOLE);

        $this->config = $config;
    }

    public function run() {
        run(function() {
            $this->isRun = true;
            $this->work();;
        });

    }

    private function work() {
        $this->client->connect($this->config->ip, $this->config->port);
        $this->client->checkValid();
        $this->client->subscribe($this->config->clientId, $this->config->destination, $this->config->filter);

        while ($this->isRun) {
            $message = $this->client->getWithoutAck(100);
            if ($entries = $message->getEntries()) {
                foreach ($entries as $entry) {

                    Fmt::println($entry);
                }
            }
        }

    }

    public function stop() {
        $this->isRun = false;
        $this->client->disConnect();
    }
}
