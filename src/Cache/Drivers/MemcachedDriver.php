<?php

namespace Phoxx\Core\Cache\Drivers;

use Memcached;
use Phoxx\Core\Cache\Cache;

class MemcachedDriver implements Cache
{
    private $memcached;

    public function __construct()
    {
        $this->memcached = new Memcached();
    }

    public function getMemcached(): Memcached
    {
        return $this->memcached;
    }

    public function addServer(string $host, int $port, int $weight)
    {
        $this->memcached->addServer($host, $port, $weight);
    }

    public function getValue(string $index)
    {
        $value = $this->memcached->get($index);

        return $this->memcached->getResultCode() === Memcached::MEMCACHED_SUCCESS ? $value : null;
    }

    public function setValue(string $index, $value, int $lifetime = 0): void
    {
        $this->memcached->set($index, $value, $lifetime);
    }

    public function removeValue(string $index): void
    {
        $this->memcached->delete($index);
    }

    public function clear(): void
    {
        $this->memcached->flush();
    }
}
