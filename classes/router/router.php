<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Router\Exceptions\RouterException;

class Router
{
	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Collection of routes for different methods.
	 * @var array
	 */
	protected $routes = array();

	protected $reversed = array();

	public function getRoute(string $path, string $method = 'GET'): ?string
	{
		return $this->routes[strtoupper($method)][$path];
	}

	public function setRoute(string $path, array $action, string $method = 'GET'): void
	{
		$method = strtoupper($method);
		$this->routes[$method][$path] = $action;
		$this->reversed[$method][key($action)][reset($action)] = $path;
	}

	public function removeRoute(string $path, string $method = 'GET'): void
	{
		$method = strtoupper($method);
		if (isset($this->routes[$method][$path]) === true) {
			unset($this->routes[$method][$path]);
		}
	}

	public function match(string $path, string $method = 'GET'): ?Route
	{
		$method = strtoupper($method);

		if (isset($this->routes[$method]) === true) {
			foreach ((array)$this->routes[$method] as $route => $action) {
				/**
				 * If path matches defined route, return
				 * route.
				 */
				if ((bool)preg_match('#^'.$route.'$#', $path, $match) === true) {
					foreach (array_keys($match) as $key) {
						if (is_numeric($key) === true) {
							unset($match[$key]);
						}
					}
					return new Route(key($action), reset($action), $match);
				}
			}
		}
		return null;
	}

	public function reverse(Route $route, string $method = 'GET'): ?string
	{
		$method = strtoupper($method);
		
		$controller = $route->getController();
		$requestMethod = $route->getMethod();
		$parameters = $route->getParameters();

		if (isset($this->reversed[$method][$controller][$requestMethod]) === true) {
			$route = $this->reversed[$method][$controller][$requestMethod];

			/**
			 * Replace named parameters in route.
			 */
			return preg_replace_callback('#\(\?P?<([a-zA-Z0-9_]*)>[^\)]+\)#', function(array $match) use ($parameters) {
				if (isset($parameters[$match[1]]) === true && (bool)preg_match('#^'.$match[0].'$#', $parameters[$match[1]]) === true) {
					return (string)$parameters[$match[1]];
				}
				throw new RouterException('Invalid value for parameter `'.$match[1].'`.');
			}, $route);
		}
		return null;
	}
}