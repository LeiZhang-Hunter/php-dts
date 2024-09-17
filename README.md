# php-dts

## 安装依赖

```
composer update
```

## 启动dts

```
php run.php start
```

## 停止dts

```
php run.php stop
```

## 重载dts

```
php run.php reload
```

## 配置示例：

```
<?php
namespace Config;
use Sink\PrintOut\PrintOut;
use Source\Canal\Source;

class Config {
    private $pid_file = "/tmp/dts.pid";

    private $service_name = "dts";

    private $worker_num = 1;

    public $reload_time = 5000;

    private $pipeline = [
        "source" => [
            [
                "handle" => Source::class,
                "config" => [
                    "ip" => "127.0.0.1",
                    "port" => 11111,
                    "clientId" => "1001",
                    "destination" => "example",
                    "filter" => ".*\\..*",
                    "reader" => [
                        "worker_count" => 1,
                    ]
                ]
            ],
        ],
        "intercept" => [

        ],
        "sink" => [
            "parallelism" => 3,
            "handle" => PrintOut::class,
            "config" => []
        ],
        "retry" => true,
    ];

    public function getConfig() {
        return [
            "pid_file" => $this->pid_file,
            "service_name" => $this->service_name,
            "worker_num" => $this->worker_num,
            "pipeline" => $this->pipeline,
            "reload_time" => $this->reload_time
        ];
    }
}

```
