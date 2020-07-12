<?php

namespace Phoxx\Core\Router;

use Phoxx\Core\Router\Exceptions\RouteException;

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
    /**
     * Replace named parameters in route.
     */
    return preg_replace_callback('#\(\?<([a-zA-Z0-9_-]+)>[^\)]+\)#', function(array $match) use ($parameters) {
      if (isset($parameters[$match[1]]) === true && (bool)preg_match('#^'.$match[0].'$#', $parameters[$match[1]]) === true) {
        return (string)$parameters[$match[1]];
      }
      throw new RouteException('Incorrect value for parameter `'.$match[1].'`.');
    }, $this->pattern);
  }
}
