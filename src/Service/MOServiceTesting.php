<?php

namespace App\Service;

final class MOServiceTesting implements RegisterInterface
{
    public function register(array $request, bool $async = false)
    {
        $authToken = '0p944K83zeKjBucsnqgFKxkD';
        $status = 0;
        if($request['text'] === 'ABRACADABRA') {
            $status = 1;
            $authToken = 'failed to register mo';
        }

        if($async) {
            return function () use($authToken, $status) {
                return new MOServiceResponse($authToken, $status);
            };
        }

        return new MOServiceResponse($authToken, $status);
    }
}
