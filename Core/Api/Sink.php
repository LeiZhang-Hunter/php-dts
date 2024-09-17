<?php
namespace Core\Api;

use Core\Event\Batch;

interface Sink extends Component {

    public function consume(Batch $batch) :int;

}
