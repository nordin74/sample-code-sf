<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RequestListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $request->setRequestFormat('json');
        if ($request->getContentType() !== 'json' && !$request->attributes->has('exception')) {
            throw new BadRequestHttpException('Bad request, required content-type header');
        }
    }
}