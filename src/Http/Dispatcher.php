<?php

namespace Phoxx\Core\Http;

use Phoxx\Core\Controllers\Controller;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Exceptions\RequestException;
use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Router\Exceptions\RouteException;
use Phoxx\Core\Router\RouteContainer;

class Router
{
  private $services;

  public function __construct(ServiceContainer $services) {
    $this->services = $services;
  }

  public function dispatch(Request $request): ?Response
  {
    if (strcasecmp($request->getServer('SERVER_NAME'), $_SERVER['SERVER_NAME']) !== 0) {
      throw new RequestException('Could not dispatch external request.');
    }

    if (($route = $this->router->match($request->getPath(), $request->getMethod(), $parameters)) === null) {
      return null;
    }

    $action = $route->getAction();
    $controller = key($action);
    $action = reset($action);

    if (
      class_exists($controller) === false ||
      is_subclass_of($controller, Controller::class) === false ||
      is_callable([$controller, $action]) === false
    ) {
      throw new RouteException('Invalid action `' . $controller . '::' . $action . '()`.');
    }

    array_push($this->requestStack, $request);

    $controller = new $controller($this, $this->services);
    $response = call_user_func_array([$controller, $action], $parameters);

    array_pop($this->requestStack);

    if (($response instanceof Response) === false) {
      throw new ResponseException('Response must be instance of `' . Response::class . '`.');
    }

    return $response;
  }

  /** TODO: Handle this in bootstrap.
  public function send(Response $response): void
  {
    if (headers_sent() === true) {
      throw new ResponseException('Response headers already sent.');
    }

    foreach ($response->getHeaders() as $name => $value) {
      header($name . ': ' . $value);
    }

    http_response_code($response->getStatus());
    print($response->getContent());
  }*/
}
