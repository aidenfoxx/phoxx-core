<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class ServiceContainer
{
	protected $services = array();	

	public function getService(string $service): ?ServiceProvider
	{
		return isset($this->services[$service]) === true ? $this->services[$service] : null;
	}

	public function setService(ServiceProvider $service): void
	{
		$this->services[$service->getServiceName()] = $service;
	}
	public function removeService(string $service): void
	{
		$this->services[$service] = $service;
	}
}