<?php

namespace App\Service;

interface ResponseInterface
{
    public function getStatus();

    public function getAuthToken();
}