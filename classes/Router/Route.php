<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Framework\ServiceContainer;

class Route
{
	private $serviceContainer;

	protected $controller;
	
	protected $action;

	protected $parameters;

	public function __construct(string $controller, string $action, array $parameters = array())
	{
		$this->serviceContainer = new ServiceContainer();
		$this->controller = $controller;
		$this->action = $action;
		$this->parameters = $parameters;
	}

	public function getServiceContainer(): ?ServiceContainer
	{
		return $this->serviceContainer;
	}

	public function setServiceContainer(ServiceContainer $serviceContainer): void
	{
		$this->serviceContainer = $serviceContainer;
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getAction(): string
	{
		return $this->action;
	}

	public function addParameter(string $value): void
	{
		$this->parameters[] = $value;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}
}