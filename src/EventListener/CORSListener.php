<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CORSListener
{
    private const ALLOW_ORIGIN_ATTR       = 'allow_origin_attr';
    private const MAX_AGE                 = 86400; // 1 day
    private const ALLOWED_REQUEST_HEADERS = ['CONTENT-TYPE', 'ORIGIN', 'X-REQUESTED-WITH', 'ACCEPT'];

    private array $allowedDomains;
    private array $allowedMethods;


    public function __construct(array $allowedDomains, array $allowedMethods)
    {
        $this->allowedDomains = $allowedDomains;
        $this->allowedMethods = $allowedMethods;
    }


    public function onKernelRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $requestHeaders = $request->headers;

        if (!$requestHeaders->has('Origin') ||
            $requestHeaders->get('Origin') === $request->getSchemeAndHttpHost()) {
            return;
        }

        $origin = $request->headers->get('Origin');
        if ($request->getMethod() === 'OPTIONS' &&
            $requestHeaders->has('Access-Control-Request-Method')) {
            $response = new Response();
            $responseHeaders = $response->headers;

            $responseHeaders->set('Access-Control-Allow-Credentials', 'true');
            $responseHeaders->set('Access-Control-Max-Age', self::MAX_AGE);
            $responseHeaders->set('Access-Control-Allow-Methods', join(',', $this->allowedMethods));
            $responseHeaders->set('Access-Control-Allow-Headers', join(',', self::ALLOWED_REQUEST_HEADERS));

            if (in_array($origin, $this->allowedDomains, true)) {
                $responseHeaders->set('Access-Control-Allow-Origin', $origin);
                if (!in_array(
                    $requestHeaders->get('Access-Control-Request-Method'),
                    $this->allowedMethods,
                    true
                )) {
                    $response->setStatusCode(405);
                } elseif ($requestHeaders->has('Access-Control-Request-Headers') &&
                    !in_array(
                        strtoupper($requestHeaders->get('Access-Control-Request-Headers')),
                        self::ALLOWED_REQUEST_HEADERS,
                        true
                    )) {
                    $response->setStatusCode(400);
                    $response->setContent(
                        'Unauthorized header ' . $requestHeaders->get('Access-Control-Request-Headers')
                    );
                }
            }

            $event->setResponse($response);
        } else {
            if (in_array($origin, $this->allowedDomains)) {
                $request->attributes->set(self::ALLOW_ORIGIN_ATTR, true);
            }
        }
    }


    public function onKernelResponse(ResponseEvent $event): void
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $allowOrigin = $request->attributes->getBoolean(self::ALLOW_ORIGIN_ATTR);

        if ($allowOrigin) {
            $response = $event->getResponse();
            $responseHeaders = $response->headers;
            $responseHeaders->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
            $responseHeaders->set('Access-Control-Allow-Credentials', 'true');
        }
    }
}