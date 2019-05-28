<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Exceptions\RequestException;
use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Router;
use Phoxx\Core\Framework\Interfaces\ServiceInterface;

class Application
{
	protected static $instances = array();

	public static function getInstance(string $name): self
	{
		if (isset(static::$instances[$name]) === false) {
			static::$instances[$name] = new static();
		}

		return static::$instances[$name];
	}

	private $router;

	private $serviceContainer = null;

	protected $requests = array();

	public function __construct()
	{
		$this->router = new Router();
		$this->serviceContainer = new ServiceContainer();
	}

	public function getRouter(): Router
	{
		return $this->router;
	}

	public function getServiceContainer(): ServiceContainer
	{
		return $this->serviceContainer;
	}

	public function setServiceContainer(ServiceContainer $serviceContainer): void
	{
		$this->serviceContainer = $serviceContainer;
	}

	public function getService(string $service): ?ServiceInterface
	{
		return $this->serviceContainer->getService($service);
	}

	public function dispatch(Request $request): ?Response
	{
		if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) !== 0) {
			throw new RequestException('Could not dispatch external Request.');
		}

		$route = $this->router->match($request->getPath(), $request->getMethod());
		
		if (($route instanceof Route) === false) {
			return null;
		}

		$route->setServiceContainer($this->serviceContainer);

		$this->requests[] = $request;

		$response = $this->router->dispatch($route);

		array_pop($this->requests);

		if (($response instanceof Response) === false) {
			throw new ResponseException('Response must be instance of `Phoxx\Core\Http\Response`.');
		}

		return $response;
	}

	public function send(Response $response): void
	{
		if (headers_sent() === true) {
			throw new ResponseException('Response headers already sent.');
		}

		foreach ($response->getHeaders() as $name => $value) {
			header($name.': '.$value, true);
		}

		http_response_code($response->getStatus());
		print($response->getContent());
	}
}