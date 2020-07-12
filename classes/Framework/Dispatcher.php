<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Framework\Exceptions\DispatchException;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Exceptions\RequestException;
use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\RouteContainer;

class Dispatcher
{
	private $routeContainer;

	private $serviceContainer;

	private $requestStack;

	public function __construct(RouteContainer $routeContainer, ServiceContainer $serviceContainer, RequestStack $requestStack)
	{
		$this->routeContainer = $routeContainer;
		$this->serviceContainer = $serviceContainer;
		$this->requestStack = $requestStack;
	}

	public function dispatch(Request $request): ?Response
	{
		if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) !== 0) {
			throw new RequestException('Could not dispatch external request.');
		}

		$route = $this->routeContainer->match($request->getPath(), $request->getMethod(), $parameters);

		if ($route === null) {
			return null;
		}

		$action = $route->getAction();
		$controller = key($action);
		$action = reset($action);

		if (class_exists($controller) === false || is_subclass_of($controller, Controller::class) === false || is_callable([$controller, $action]) === false) {
			throw new DispatchException('Invalid action `'.$controller.'::'.$action.'()`.');
		}

		$this->requestStack->push($request);

		$controller = new $controller($this->routeContainer, $this->serviceContainer, $this->requestStack);
		$response = call_user_func_array(array($controller, $action), $parameters);

		$this->requestStack->pop($request);

		if (($response instanceof Response) === false) {
			throw new ResponseException('Response must be instance of `'.Response::class.'`.');
		}

		return $response;
	}
}
