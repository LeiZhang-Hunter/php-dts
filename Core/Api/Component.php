<?php
namespace Core\Api;

interface Component {
    public function init(Context $context);
    public function Start(); // nonblock
    public function Stop();
}
