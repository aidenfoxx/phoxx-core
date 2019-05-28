<?php 

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Interfaces\CacheDriver;

class ApcuDriver implements CacheDriver
{
	public function getValue(string $index)
	{
		$sucess = false;
		$value = apcu_fetch($index, $sucess);
		
		return $sucess === true ? $value : null;
	}

	public function setValue(string $index, $value, int $lifetime = 0): void
	{
		apcu_store($index, $value, $lifetime);
	}

	public function removeValue(string $index): void
	{
		apcu_delete($index);
	}

	public function clear(): void
	{
		apcu_clear_cache();
	}
}