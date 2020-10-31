<?php

namespace App\Service;

final class MOService implements RegisterInterface
{
    private string $executableFile;

    public function __construct()
    {
        $executableFile = __DIR__ . '/../../bin/registermo';
        if(!file_exists($executableFile)) {
            throw new \Exception('Executable not found: ' . $executableFile);
        }
        $this->executableFile = $executableFile;
    }


    public function register(array $request, bool $async = false)
    {
        $jsonRequest = json_encode($request);
        $filePointer = popen("$this->executableFile $jsonRequest", 'r');
        if($async) {
            return function () use($filePointer) {
                return new MOServiceResponse(stream_get_contents($filePointer), pclose($filePointer));
            };
        }

        return new MOServiceResponse(stream_get_contents($filePointer), pclose($filePointer));
    }
}
