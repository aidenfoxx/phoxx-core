<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\Exceptions\DispatcherException;

class Dispatcher
{
	/**
	 * Core instance of the class.
	 * @var Dispatcher
	 */
	protected static $instance;

	/**
	 * Core instance.
	 * @return Dispatcher Core instance of the class.
	 */
	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			static::$instance = new static(Router::core());
		}
		return static::$instance;
	}

	/**
	 * The router used to resolve requests.
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * List of active requests (first being the 
	 * initial request and the last being the latest)
	 * @var array
	 */
	protected $requests = array();	

	/**
	 * Construct the class.
	 * @param Router $router Router to be used to match requests.
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	/**
	 * The first request created.
	 * @return Request A request object
	 */
	public function main(): ?Request
	{
		return ($request = reset($this->requests)) !== false ? $request : null;
	}

	/**
	 * The active request.
	 * @return Request A request object
	 */
	public function active(): ?Request
	{
		return ($request = end($this->requests)) !== false ? $request : null;
	}

	/**
	 * Execute a request and return response.
	 * @param  Request $request A request to execute
	 * @return Response A response object
	 */
	public function dispatch(Request $request): ?Response
	{
		if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) === 0) {
			$response = null;

			array_push($this->requests, $request);

			if (($route = $this->router->match($request->getPath(), $request->getMethod())) instanceof Route) {
				$response = $route->execute()->getResponse();
			}

			array_pop($this->requests);

			if ($response instanceof Response) {
				return $response;
			}
		} else {
			throw new DispatcherException('Could not dispatch external Request.');
		}
		return null;
	}

	/**
	 * Clear the current request chain.
	 * @return void
	 */
	public function clear(): void
	{
		$this->requests = array();
	}
}