<?php
/**
 * 命令管理
 */
namespace Core\Pipeline;

use Core\Api\Context;
use Core\Api\Result;
use Core\Api\Sink;
use Core\Channel\Queue;
use Core\Event\Batch;
use Swoole\Coroutine\WaitGroup;
use function Swoole\Coroutine\run;
use Swoole\Coroutine;

class Pipeline {

    /**
     * @var Queue
     */
    private $queue;

    private $interceptors = [];

    private $sources = [];

    /**
     * @var Sink
     */
    private $sink;

    private $parallelism = 1;

    private $wg;

    /**
     * @var bool
     */
    private $isRun = false;

    /**
     * @var bool
     */
    private $isRetry = false;

    public function __construct($config) {
        // 注入factory
        $this->queue = new Queue();
        $this->wg = new WaitGroup();

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
                if (isset($config["sink"]["config"])) {
                    $context = new Context($config["sink"]["config"]);
                    $this->sink->init($context);
                } else {
                    $context = new Context([]);
                    $this->sink->init($context);
                }
            }
        }

        if (isset($config["parallelism"])) {
            $this->parallelism = $config["parallelism"];
        }

        if (isset($config["retry"])) {
            $this->isRetry = $config["retry"];
        }
    }

    /**
     * 输出sink
     * @return void
     */
    public function out() {

    }

    public function run() {
        if (sizeof($this->sources) == 0) {
            throw new \Exception("source is empty");
        }

        if (!$this->sink) {
            throw new \Exception("sink is empty");
        }

        $this->isRun = true;

        $this->queue->start();

        $productFun = function ($event) {
            foreach ($this->interceptors as $k => $v) {
                $v->intercept($event);
            }
            $this->queue->in($event);
        };

        $parallelism = $this->parallelism;
        for ($i = 0; $i < $parallelism; $i++) {
            \Swoole\Coroutine::create(function () {
                $this->sinkInvokeLoop();
            });
        }

        $this->sink->start();

        // 启动Source

        foreach ($this->sources as $k => $v) {
            $v->ProductLoop($productFun);
            $v->start();
        }


    }

    public function stop() {
        // close Source
        foreach ($this->sources as $k => $v) {
            $v->stop();
        }
        $this->isRun = false;
        $this->queue->stop();
        $this->wg->wait();
    }

    private function sinkInvokeLoop() {
        $this->wg->add(1);
        \Swoole\Coroutine::defer(function () {
            $this->wg->done();
        });
        while ($this->isRun) {
            /**
             * @var Batch $batch
             */
            $batch = $this->queue->out();

            if ($batch) {
                $result = $this->sink->consume($batch);
                if ($result == Result::$Success) {
                    $batch->ack();
                    continue;
                }

                if ($result === Result::$Faield) {
                    if ($this->isRetry) {
                        $this->queue->in($batch);
                    }
                    continue;
                }

                if ($result == Result::$Drop) {
                    $batch->ack();
                    continue;
                }
                continue;
            }
        }
    }
}
