<?php

namespace Core\Api;

use Core\Event\Batch;

interface Queue {
    public function in(Batch& $event);

    public function out();
}
