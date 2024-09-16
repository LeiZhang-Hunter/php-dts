<?php
namespace Core\Api;

class Context {
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function data() {
        return $this->data;
    }
}
