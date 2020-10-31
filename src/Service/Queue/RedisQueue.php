<?php

namespace App\Service\Queue;

/** @final */
class RedisQueue  implements QueueInterface
{
    private \Redis $redis;
    private string $channel;
    private ?string $backupChannel = null;

    public function __construct(\Redis $redis, $channel)
    {
        $this->redis = $redis;
        $this->channel = $channel;
    }


    public function pull()
    {
        return $this->redis->lPop($this->channel);
    }


    public function push($message)
    {
        $this->redis->rpush($this->channel, $message);
    }


    public function pullAndBackUp()
    {
        return $this->redis->rpoplpush($this->channel, $this->generateBackupChannel());
    }


    private function generateBackupChannel()
    {
        if ($this->backupChannel === null) {
            $this->backupChannel = 'backup-' . microtime(true) . '-' . uniqid();
        }

        return $this->backupChannel;
    }


    public function clearBackUp()
    {
        return $this->redis->del($this->channel);
    }
}