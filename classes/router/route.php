<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Router\Exceptions\RouteException;

class Route
{
	protected $controller;
	
	protected $method;

	protected $parameters = array();

	protected $response = null;

	public function __construct(string $controller, string $method, array $parameters = array())
	{
		$this->controller = $controller;
		$this->method = $method;
		$this->parameters = $parameters;
	}

    public function getResponse()
    {
    	return $this->response;
    }

	public function getController(): string
	{
		return $this->controller;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getParameter(string $name)
	{
		return isset($this->parameters[$name]) === true ? $this->parameters[$name] : null;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function setParameter(string $name, string $value): void
	{
		$this->parameters[$name] = $value;
	}

    public function execute(): self
    {
		if (class_exists($this->controller) === true && 
			is_subclass_of($this->controller, 'Phoxx\Core\Controllers\BaseController') === true && 
			is_callable(array($this->controller, $this->method)) === true) {
			$this->response = call_user_func_array(array(
					new $this->controller(), 
					$this->method
				), 
				$this->parameters
			);
			return $this;
		}
		throw new RouteException('Invalid route `'.$this->controller.'::'.$this->method.'()`.');
    }
}