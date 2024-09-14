<?php
namespace Process;

abstract class SwooleProcess
{
    abstract function __construct($hook);
    abstract function init($process);
    abstract function run();
}