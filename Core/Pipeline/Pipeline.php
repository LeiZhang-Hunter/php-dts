<?php
/**
 * 命令管理
 */
namespace Core\Pipeline;

use Core\Api\Context;
use Core\Channel\Queue;

class Pipeline {

    /**
     * @var Queue
     */
    private $queue;

    private $interceptors = [];

    private $sources = [];

    private $sink;

    public function __construct($config) {
        // 注入factory
        $this->queue = new Queue();

        if (isset($config["source"])) {
            foreach ($config["source"] as $key => $value) {
                if (!isset($value["handle"])) {
                    continue;
                }
                if (!isset($value["config"])) {
                    continue;
                }
                $handle = new $value["handle"]();
                $context = new Context($value["config"]);
                $handle->init($context);
                $this->sources[] = $handle;
            }
        }

        if (isset($config["intercept"])) {
            foreach ($config["intercept"] as $key => $value) {
                if (!isset($value["handle"])) {
                    continue;
                }
                $handle = new $value["handle"]();

                $this->interceptors[] = $handle;
            }
        }

        if (isset($config["sink"])) {
            if (isset($config["sink"]["handle"])) {
                $this->sink = new $config["sink"]["handle"]();
            }
        }
    }

    public function run() {
        if (sizeof($this->sources) == 0) {
            throw new \Exception("source is empty");
        }

        if (!$this->sink) {
            throw new \Exception("sink is empty");
        }

        // 启动Source
        foreach ($this->sources as $k => $v) {
            $v->start();
        }
        $this->sink->start();
    }

    public function stop() {

    }
}
