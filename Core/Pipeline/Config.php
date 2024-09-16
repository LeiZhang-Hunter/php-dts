<?php

namespace Core\Pipeline;

use Core\Channel\Queue;

class Config {

    private $source;

    private $intercept;

    private $sink;

    public function load($config) {

        $this->queue = new Queue();

    }
}
