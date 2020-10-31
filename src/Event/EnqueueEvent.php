<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EnqueueEvent extends Event
{
    public const NAME = 'enqueue.event';

    protected array $subscription;


    public function __construct(array $subscription)
    {
        $this->subscription = $subscription;
    }


    public function getSubscription(): array
    {
        return $this->subscription;
    }
}