<?php

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Interfaces\CacheDriver;

use EngineException;
use Redis;

class RedisDriver implements CacheDriver
{
  private $redis;

  public function __construct(string $address, int $port)
  {
    $this->redis = new Redis();
    $this->redis->connect($address, $port);
  }

  public function getRedis(): Memcached
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
