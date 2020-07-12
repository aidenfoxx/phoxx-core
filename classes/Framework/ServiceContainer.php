<?php

namespace Phoxx\Core\Framework;

class ServiceContainer
{
	protected $services = [];

	public function getService(string $index): ?object
	{
		return isset($this->services[$index]) === true ? $this->services[$index] : null;
	}

	public function addService(object $service): void
	{
		$this->services[get_class($service)] = $service;
	}

	public function removeService(string $index): void
	{
		unset($this->services[$index]);
	}
}
