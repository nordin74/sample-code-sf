<?php

namespace App\EventSubscriber;

use App\Event\EnqueueEvent;
use App\Service\Queue\QueueInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EnqueueSubscriber implements EventSubscriberInterface
{
    private QueueInterface $queue;
    private array $data = [];


    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }


    public static function getSubscribedEvents()
    {
        return [
            EnqueueEvent::class     => [['onEnqueueEvent']],
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }


    public function onEnqueueEvent(EnqueueEvent $event)
    {
        $this->data = $event->getSubscription();
    }


    // To flush the response asap => reach fastcgi_finish_request call
    public function onKernelTerminate()
    {
        if (!empty($this->data)) {
            $this->queue->push(json_encode($this->data));
        }
    }
}