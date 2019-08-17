<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Exceptions\RequestException;
use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Router;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;
use Phoxx\Core\Controllers\Exceptions\ControllerException;

class Application
{
	protected static $instances = array();

	public static function getInstance(string $name): self
	{
		if (isset(static::$instances[$name]) === false) {
			$router = new Router();
			$serviceContainer = new ServiceContainer();

			static::$instances[$name] = new static($router, $serviceContainer);
		}

		return static::$instances[$name];
	}

	private $routeContainer;

	private $serviceContainer;

	public function __construct(RouteContainer $routeContainer, ServiceContainer $serviceContainer)
	{
		$this->routeContainer = $routeContainer;
		$this->serviceContainer = $serviceContainer;
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

	public function getService(string $service): ?ServiceProvider
	{
		return $this->serviceContainer->getService($service);
	}

	public function dispatch(Request $request): ?Response
	{
		if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) !== 0) {
			throw new RequestException('Could not dispatch external Request.');
		}

		$route = $this->router->match($request->getPath(), $request->getMethod());
		
		if ($route === null) {
			return null;
		}

		$controller = $route->getController();
		$action = $route->getAction();
		$parameters = $route->getParameters();

		if (class_exists($controller) === false || 
			is_subclass_of($controller, 'Phoxx\Core\Controllers\Controller') === false || 
			is_callable(array($controller, $action)) === false) {
				throw new ControllerException('Invalid action `'.$controller.'::'.$action.'()`.');
		}

		$controller = new $controller($request, $this->serviceContainer);
		$response = call_user_func_array(array($controller, $action), $parameters);

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