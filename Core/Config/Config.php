<?php
namespace Config;
class Config {
    private $pid_file = "/tmp/dts.pid";

    private $service_name = "dts";

    private $worker_num = 1;
    
    public function getConfig() {
        return [
            "pid_file" => $this->pid_file,
            "service_name" => $this->service_name,
            "worker_num" => $this->worker_num,
        ];
    }
}