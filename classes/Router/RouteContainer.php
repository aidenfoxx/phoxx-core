<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Exceptions\RouteException;

class Router
{
	protected $routes = array();

	protected $reversed = array();

	public function getRoutes(): array
	{
		$this->routes;
	}

	/**
	 * TODO: Accept Route.
	 */
	public function getRoute(string $path, string $method = 'GET'): array
	{
		return $this->routes[$method][$path];
	}

	public function setRoute(string $path, array $action, string $method = 'GET'): void
	{
		$method = strtoupper($method);

		$this->routes[$method][$path] = $action;
		$this->reversed[$method][key($action)][reset($action)] = $path;
	}

	/**
	 * TODO: https://symfony.com/blog/new-in-symfony-4-1-fastest-php-router
	 */
	public function match(string $path, string $method = 'GET'): ?array
	{
		$method = strtoupper($method);

		if (isset($this->routes[$method]) === false) {
			return null;
		}

		foreach ($this->routes[$method] as $route => $action) {
			/**
			 * If path matches defined route, return
			 * route.
			 */
			if ((bool)preg_match('#^'.$route.'$#', $path, $match) === true) {
				/**
				 * Ignore named keys.
				 */
				foreach (array_keys($match) as $key) {
					if (is_numeric($key) === false) {
						unset($match[$key]);
					}
				}

				return array(
					'controller' => key($action),
					'action' => reset($action),
					'parameters' => array_slice($match, 1)
				);
			}
		}

		return null;
	}

	public function reverse(string $controller, string $action, array $parameters = array(), string $method = 'GET'): ?string
	{
		$method = strtoupper($method);

		if (isset($this->reversed[$method][$controller][$action]) === false) {
			return null;
		}

		$route = $this->reversed[$method][$controller][$action];

		/**
		 * Replace named parameters in route.
		 */
		return preg_replace_callback('#\(\?P?<([a-zA-Z0-9_]*)>[^\)]+\)#', function(array $match) use ($parameters) {
			if (isset($parameters[$match[1]]) === true && (bool)preg_match('#^'.$match[0].'$#', $parameters[$match[1]]) === true) {
				return (string)$parameters[$match[1]];
			}
			throw new RouteException('Invalid value for parameter `'.$match[1].'`.');
		}, $route);
	}
}