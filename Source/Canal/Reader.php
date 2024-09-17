<?php

namespace Source\Canal;

use Core\Event\Batch;
use xingwenge\canal_php\CanalClient;
use xingwenge\canal_php\CanalConnectorFactory;
use xingwenge\canal_php\Fmt;
use function Swoole\Coroutine\run;
use function Swoole\Coroutine\go;
use \Swoole\Coroutine;

class Reader {

    /**
     * @var \xingwenge\canal_php\adapter\swoole\CanalConnector
     */
    private $client;

    private $config;

    private $isRun = false;

    public function __construct(Config $config) {
        $this->client = CanalConnectorFactory::createClient(CanalClient::TYPE_SOCKET);

        $this->config = $config;
    }

    public function start($productFunc) {
        Coroutine::create(function () use ($productFunc) {
            $this->isRun = true;
            $this->work($productFunc);;
        });
    }

    private function work($productFunc) {
        $this->client->connect($this->config->ip, $this->config->port);
        $this->client->checkValid();
        $this->client->subscribe($this->config->clientId, $this->config->destination, $this->config->filter);

        while ($this->isRun) {

            $message = $this->client->getWithoutAck(100);
            if ($entries = $message->getEntries()) {
                $batch = new Batch($entries, function () use($message) {
                    $this->client->ack($message->getId());
                });
                $productFunc($batch);
            }
        }
    }

    public function stop() {
        $this->isRun = false;
        $this->client->disConnect();
    }

}
