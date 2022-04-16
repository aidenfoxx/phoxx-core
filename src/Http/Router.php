<?php

namespace Phoxx\Core\Http;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Exceptions\RequestException;
use Phoxx\Core\Exceptions\ResponseException;
use Phoxx\Core\Exceptions\RouteException;
use Phoxx\Core\System\Services;

class Router
{
    private $services;

    private $routes = [];

    private $requests = [];

    public function __construct(Services $services)
    {
        $this->services = $services;
    }

    public function addRoute(Route $route): void
    {
        $method = $route->getMethod();
        $pattern = $route->getPattern();

        $this->routes[$method][$pattern] = $route;
    }

    public function match(string $path, string $method = 'GET', &$parameters = []): ?Route
    {
        $method = strtoupper($method);

        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $pattern => $route) {
            if (!preg_match('#^' . $pattern . '$#', $path, $match)) {
                continue;
            }

            // Find named keys
            foreach (array_keys($match) as $key) {
                if (is_numeric($key)) {
                    unset($match[$key]);
                }
            }

            $parameters = $match;

            return $route;
        }

        return null;
    }

    public function dispatch(Request $request): ?Response
    {
        if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) !== 0) {
            throw new RequestException('Could not dispatch external request.');
        }

        if (!($route = $this->match($request->getPath(), $request->getMethod(), $parameters))) {
            return null;
        }

        $action = $route->getAction();
        $controller = key($action);
        $method = reset($action);

        if (
            !class_exists($controller) ||
            !is_subclass_of($controller, Controller::class) ||
            !is_callable([$controller, $method])
        ) {
            throw new RouteException('Invalid action `' . $controller . '::' . $method . '()`.');
        }

        array_push($this->requests, $request);

        $controller = new $controller($this, $this->services);
        $response = call_user_func_array([$controller, $method], $parameters);

        array_pop($this->requests);

        if (!($response instanceof Response)) {
            throw new ResponseException('Response must be instance of `' . Response::class . '`.');
        }

        return $response;
    }

    public function main(): ?Request
    {
        return ($request = reset($this->requests)) ? $request : null;
    }

    public function active(): ?Request
    {
        return ($request = end($this->requests)) ? $request : null;
    }
}
