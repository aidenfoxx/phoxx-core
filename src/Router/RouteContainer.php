<?php

namespace Phoxx\Core\Router;

class RouteContainer
{
  // TODO: $routes should contain sub-arrays for each method
  protected $routes = [];

  public function getRoute(string $pattern, string $method = 'GET'): ?Route
  {
    $method = strtoupper($method);

    return isset($this->routes[$method][$pattern]) === true ? $this->routes[$method][$pattern] : null;
  }

  public function setRoute(Route $route): void
  {
    $method = $route->getMethod();
    $pattern = $route->getPattern();

    $this->routes[$method][$pattern] = $route;
  }

  public function removeRoute(string $pattern, string $method = 'GET'): void
  {
    $method = strtoupper($method);

    if (isset($this->routes[$method][$pattern]) === true) {
      unset($this->routes[$method][$pattern]);
    }
  }

  public function match(string $path, string $method = 'GET', &$parameters = []): ?Route
  {
    $method = strtoupper($method);

    if (isset($this->routes[$method]) === false) {
      return null;
    }

    foreach ($this->routes[$method] as $pattern => $route) {
      /**
       * If path matches defined route, return
       * route.
       */
      if ((bool)preg_match('#^' . $pattern . '$#', $path, $match) === false) {
        continue;
      }

      /**
       * Find named keys.
       */
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
}
