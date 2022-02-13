<?php

namespace Phoxx\Core\Http;

use Phoxx\Core\Exceptions\RouteException;

class Route
{
  protected $pattern;

  protected $action;

  protected $method;

  public function __construct(string $pattern, array $action, string $method = 'GET')
  {
    $this->pattern = $pattern;
    $this->action = $action;
    $this->method = strtoupper($method);
  }

  public function getPattern(): string
  {
    return $this->pattern;
  }

  public function getAction(): array
  {
    return $this->action;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function reverse(array $parameters = []): string
  {
    // Replace named parameters in route.
    return preg_replace_callback('#\(\?<([a-zA-Z0-9_-]+)>[^\)]+\)#', function (array $match) use ($parameters) {
      list($pattern, $parameter) = $match;

      if (isset($parameters[$parameter]) === true && (bool)preg_match('#^' . $pattern . '$#', $parameters[$parameter]) === true) {
        return (string)$parameters[$parameter];
      }
      throw new RouteException('Incorrect value for parameter `' . $parameter . '`.');
    }, $this->pattern);
  }
}