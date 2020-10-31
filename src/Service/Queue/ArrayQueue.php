<?php

namespace App\Service\Queue;


final class ArrayQueue implements QueueInterface
{
    private array $mainQueue = [];
    private array $backUpQueue = [];


    public function pull()
    {
        return array_pop($this->mainQueue);
    }


    public function push($message)
    {
        return array_push($this->mainQueue, $message);
    }


    public function pullAndBackUp()
    {
        $item = $this->pull();
        array_push($this->backUpQueue, $item);

        return $item;
    }


    public function clearBackUp()
    {
        $this->backUpQueue = [];
    }
}