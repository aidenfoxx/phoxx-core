<?php

namespace Phoxx\Core\Http\Session;

use Phoxx\Core\Http\Session\Interfaces\SessionDriver;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Session implements ServiceProvider
{
	protected $driver;

	public function __construct(SessionDriver $driver)
	{
		$this->driver = $driver;
	}

	public function getServiceName(): string
	{
		return 'session';
	}

	public function getDriver(): SessionDriver
	{
		return $this->driver;
	}

	public function getValue(string $index)
	{
		return $this->driver->getValue($index);
	}

	public function flashValue(string $index)
	{
		return $this->driver->flashValue($index);
	}

	public function setValue(string $index, $value): void
	{
		$this->driver->setValue($index, $value);
	}

	public function removeValue(string $index): void
	{
		$this->driver->removeValue($index);
	}

	public function active(): bool
	{
		return $this->driver->active();
	}

	public function open(): bool
	{
		return $this->driver->open();
	}

	public function close(): bool
	{
		return $this->driver->close();
	}

	public function clear(): void
	{
		$this->driver->clear();
	}
}