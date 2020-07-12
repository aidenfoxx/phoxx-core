<?php

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Interfaces\CacheDriver;

class ArrayDriver implements CacheDriver
{
	protected $cache = [];

	public function getValue(string $index)
	{
		if (isset($this->cache[$index]) === false) {
			return null;
		}

		$lifetime = $this->cache[$index]['lifetime'];

		return $lifetime === 0 || $lifetime > time() ? $this->cache[$index]['value'] : null;
	}

	public function setValue(string $index, $value, int $lifetime = 0): void
	{
		$this->cache[$index] = [
			'value' => $value,
			'lifetime' => $lifetime !== 0 ? time() + $lifetime : $lifetime
		];
	}

	public function removeValue(string $index): void
	{
		unset($this->cache[$index]);
	}

	public function clear(): void
	{
		$this->cache = [];
	}
}

