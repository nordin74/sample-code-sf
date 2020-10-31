<?php

namespace App\Service\Queue;

interface QueueInterface
{
    public function pull();

    public function push($message);

    public function pullAndBackUp();

    public function clearBackUp();
}