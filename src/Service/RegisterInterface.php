<?php

namespace App\Service;

interface RegisterInterface
{
    public function register(array $request, bool $async = false);
}