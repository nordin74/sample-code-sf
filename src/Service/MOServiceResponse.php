<?php

namespace App\Service;

final class MOServiceResponse implements ResponseInterface
{
    private int $status;
    private string $authToken;

    public function __construct(string $authToken, int $status)
    {
        $this->authToken = $authToken;
        $this->status = $status;
    }


    public function getAuthToken()
    {
        return $this->authToken;
    }


    public function getStatus()
    {
        return  !$this->status;
    }
}