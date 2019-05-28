<?php

namespace Phoxx\Core\Cache;

use Phoxx\Core\Cache\Interfaces\CacheDriver;
use Phoxx\Core\Framework\Interfaces\ServiceInterface;

class Cache implements ServiceInterface
{
	private $driver;
	
	public function __construct(CacheDriver $driver)
	{
		$this->driver = $driver;
	}

	public function getServiceName(): string
	{
		return 'cache';
	}

	public function getDriver(): CacheDriver
	{
		return $this->driver;
	}

	public function getValue(string $index)
	{
		return $this->driver->getValue($index);
	}
	
	public function setValue(string $index, $value): void
	{
		$this->driver->setValue($index, $value);
	}

	public function removeValue(string $index): void
	{
		$this->driver->removeValue($index);
	}

	public function clear(): void
	{
		$this->driver->clear();
	}
}