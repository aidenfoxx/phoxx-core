<?php 

namespace Phoxx\Core\Cache\Interfaces;

interface CacheDriver
{
	public function getValue(string $index);
	
	public function setValue(string $index, $value, int $lifetime): void;

	public function removeValue(string $index): void;

	public function clear(): void;
}