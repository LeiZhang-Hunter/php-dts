<?php

namespace Core\Channel;

use Core\Api\Component;
use Core\Api\Context;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine;
use Swoole\Coroutine\WaitGroup;

class Queue implements Component {


    private $in;

    private $wg;

    public function __construct() {
        $this->in = new Channel(16);
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
        $this->wg->add();
        // TODO: Implement init() method.
        Coroutine::create(function () {
            $this->worker();
            $this->wg->done();
        });
    }

    public function stop() {
        $this->in->close();
        $this->wg->wait();
    }

    private function worker() {
        while(1) {
            $data = $this->in->pop(2.0);
            if ($data) {
                var_dump($data);
            } else {
                if ($this->in->errCode === SWOOLE_CHANNEL_TIMEOUT) {

                } else {
                    break;
                }
            }
        }
    }
}
