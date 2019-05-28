<?php 

namespace Phoxx\Core\Cache\Drivers;

use Redis;

use Phoxx\Core\Cache\Interfaces\CacheDriver;

class RedisDriver implements CacheDriver
{
	private $memcached;

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
		return ($value = @unserialize($this->redis->get($index))) === false ? null : $value;
	}

	public function setValue(string $index, $value, int $lifetime): void
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