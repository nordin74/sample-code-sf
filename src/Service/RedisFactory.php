<?php

namespace App\Service;

class RedisFactory
{
    public function __construct()
    {
        $this->redis = new \Redis();
    }


    public function build(
        string $host,
        int $port = 6379,
        float $timeout = 0.0,
        $reserved = null,
        $retryInterval = 0,
        $readTimeout = 0.0
    ): \Redis
    {
        $this->redis->connect($host, $port, $timeout, $reserved, $retryInterval, $readTimeout);

        return $this->redis;
    }
}