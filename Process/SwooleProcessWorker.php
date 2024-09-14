<?php

namespace Process;

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

    private $hook;

    public function __construct($hook, $index = 0)
    {
        $this->hook = $hook;
        $this->worker = new Process([$this, "init"]);
        $this->worker->set([
            'enable_coroutine' => false
        ]);
        $this->worker->index = $index;
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
        while (SwooleProcessManager::getSyncPrimitive()->get()) {
            $data = $socket->recv();
        }
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
        if ($this->hook) {
            call_user_func_array($this->hook, [$process]);
        }
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