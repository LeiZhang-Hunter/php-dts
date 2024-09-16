<?php

namespace Source\Canal;

class ReaderConfig {
    // 并发数
    public $workerCount = 1;

    public function init($data) {
        if (isset($data["worker_count"])) {
            $this->workerCount = $data["worker_count"];
        }
    }
}
