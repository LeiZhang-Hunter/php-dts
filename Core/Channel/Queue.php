<?php

namespace Core\Channel;

use Core\Api\Context;
use Core\Event\Batch;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine;
use Swoole\Coroutine\WaitGroup;

class Queue implements \Core\Api\Queue {


    private $in;

    private $wg;

    public function __construct() {
        $this->in = new Channel(1);
        $this->wg = new WaitGroup();
    }


    public function Type() {

    }

    public function Category() {

    }

    public function Config() {

    }

    public function String() {

    }

    public function init(Context $context)
    {
        // TODO: Implement init() method.
    }

    public function start()
    {

    }

    public function stop() {
        $ret = $this->in->close();
    }

    public function in(Batch& $event)
    {
        $this->in->push($event);
    }

    public function out() {
        return $this->in->pop();
    }
}
