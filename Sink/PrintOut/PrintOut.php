<?php
namespace Sink\PrintOut;

use Core\Api\Context;
use Core\Api\Result;
use Core\Api\Sink;
use Core\Event\Batch;
use xingwenge\canal_php\Fmt;

class PrintOut implements Sink {

    public function init(Context $context)
    {
        // TODO: Implement init() method.
    }

    public function Start()
    {
        // TODO: Implement Start() method.
    }

    public function Stop()
    {
        // TODO: Implement Stop() method.
    }

    /**
     * @param $batch
     * @return int
     * @throws \Exception
     */
    public function consume(Batch $batch) :int
    {
        foreach ($batch->getData() as $entry) {
            Fmt::println($entry);
        }
        return Result::$Success;
    }
}
