<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Http\Request;
use Phoxx\Core\Controllers\Exceptions\ControllerException;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;
use Phoxx\Core\Framework\Exceptions\ServiceException;

abstract class Controller
{
	private $request;

	private $serviceContainer;

	/**
	 * TODO: Make RequestContainer Dispatcher and have Router as part of it.
	 */
	public function __construct(RouteContainer $routeContainer, ServiceContainer $serviceContainer)
	{
		$this->routeContainer = $routeContainer;
		$this->serviceContainer = $serviceContainer;
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function getService(string $service): ?ServiceProvider
	{
		return $this->serviceContainer->getService($service);
	}

	public function postValue(string $index, $default)
	{
		if (isset($_POST[$index]) === true) {
			return $_POST[$index];
		}

		return null;
	}

	public function getValue(string $index, $default)
	{
		if (isset($_GET[$index]) === true) {
			return $_GET[$index];
		}

		return null;
	}

	/**
	 * TODO: Implement.
	 */
	public function dispatch(Request $request): Response
	{
		$controller = key($action);
		$action = reset($action);

		if (class_exists($controller) === false || 
			is_subclass_of($controller, 'Phoxx\Core\Controllers\Controller') === false || 
			is_callable(array($controller, $action)) === false) {
				throw new ControllerException('Invalid action `'.$controller.'::'.$action.'()`.');
		}

		$controller = new $controller($this->request, $this->serviceContainer);

		return call_user_func_array(array($controller, $action), $parameters);
	}

	public function redirect(string $uri): Response
	{
		return new Response($uri, array('Location' => $url));
	}
}