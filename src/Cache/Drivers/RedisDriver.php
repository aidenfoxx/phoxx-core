<?php

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Cache;
use Redis;

class RedisDriver implements Cache
{
    private $redis;

    public function __construct(string $host, int $port)
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

    public function getValue(string $index)
    {
        $value = $this->redis->get($index);

        return ($output = @unserialize($value)) !== false || $value === 'b:0;' ? $output : null;
    }

    public function setValue(string $index, $value, int $lifetime = 0): void
    {
        $this->redis->set($index, serialize($value), $lifetime);
    }

    public function removeValue(string $index): void
    {
        $this->redis->delete($index);
    }

    public function clear(): void
    {
        $this->redis->flushAll();
    }
}
