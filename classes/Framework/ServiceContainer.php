<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Framework\Interfaces\ServiceInterface;

class ServiceContainer
{
	protected $services = array();	

	public function getService(string $service): ?ServiceInterface
	{
		return isset($this->services[$service]) === true ? $this->services[$service] : null;
	}

	public function setService(ServiceInterface $service): void
	{
		$this->services[$service->getServiceName()] = $service;
	}
	public function removeService(string $service): void
	{
		$this->services[$service] = $service;
	}
}