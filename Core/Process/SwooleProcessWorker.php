<?php

namespace Core\Process;

use Core\Log\Logger;
use Core\Pipeline\Pipeline;
use  Swoole\Process;

class SwooleProcessWorker extends SwooleProcess
{

    /**
     * 工作进程
     * @var Process
     */
    private $worker;

    /**
     * 进程id
     * @var int
     */
    private $pid;

    /**
     * 工作进程名字
     * @var
     */
    private $name;

    private $pipeline;


    public function __construct($hook, $index = 0, $pipeline = [])
    {
        $this->hook = $hook;
        $this->worker = new Process([$this, "init"]);
        $this->worker->set([
            'enable_coroutine' => false
        ]);
        $this->worker->index = $index;
        $this->pipeline = $pipeline;
    }

    /**
     * 获取进程pid
     * @return int
     */
    public function getProcessId()
    {
        return $this->pid;
    }

    /**
     * 设置进程名字
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function output($pid)
    {
        $socket = $this->worker->exportSocket();
    }

    /**
     * 初始化进程运行，目的是为了设置进程名字
     * @param $process
     */
    public function init($process)
    {
        if ($this->name) {
            swoole_set_process_name($this->name . "-worker");
        }
        \Swoole\Runtime::enableCoroutine( SWOOLE_HOOK_TCP);
        swoole_async_set([
            'enable_coroutine' => true
        ]);
        $pipeline = new Pipeline($this->pipeline);

        $pipeline->run();


//        $pipeline->stop();
        \Swoole\Process::signal(SIGTERM, function () use ($pipeline) {
            Logger::getLogger()->info("php-dts begin stop");
            $pipeline->stop();
            Logger::getLogger()->info("php-dts has stop");
        });

        Logger::getLogger()->info("php-dts has finish");
    }

    /**
     * 运行进程
     * @return int
     */
    public function run()
    {
        $this->pid = $this->worker->start();
        return $this->pid;
    }
}
