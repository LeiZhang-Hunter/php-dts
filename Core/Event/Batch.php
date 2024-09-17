<?php
namespace Core\Event;

class Batch {
    private $ackFunc;

    private $data;

    public function __construct($data, $ack) {
        $this->data = $data;

        $this->ackFunc = $ack;
    }

    public function getData() {
        return $this->data;
    }

    public function ack() {
        call_user_func($this->ackFunc);
    }
}
