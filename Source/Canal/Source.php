<?php

namespace Source\Canal;

use Core\Api\Context;

class Source implements \Core\Api\Source {

    private $readers = [];


    private $config;

    private $productFun;

    public function __construct() {
        $this->config = new Config();
    }

    public function init(Context $context)
    {
        $this->config->load($context->data());
        for ($i = 0; $i < $this->config->readerConfig->workerCount; $i++) {
            $reader = new Reader($this->config);
            $this->readers[] = $reader;
        }

    }

    public function Start()
    {

        foreach ($this->readers as $key => $v) {
            $v->start($this->productFun);
        }

    }

    public function Stop()
    {
        // TODO: Implement Stop() method.
        foreach ($this->readers as $key => $v) {
            $v->stop();
        }
    }

    public function ProductLoop($productFunc)
    {
        $this->productFun = $productFunc;
    }
}
