<?php

namespace Phoxx\Core\Framework;

use Phoxx\Core\Framework\Dispatcher;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Exceptions\ResponseException;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\RouteContainer;

class Application extends Dispatcher
{
  private $routeContainer;

  private $serviceContainer;

  public function __construct()
  {
    $this->routeContainer = new RouteContainer();
    $this->serviceContainer = new ServiceContainer();

    parent::__construct($this->routeContainer, $this->serviceContainer, new RequestStack());
  }

  public function getRouteContainer(): RouteContainer
  {
    return $this->routeContainer;
  }

  public function getServiceContainer(): ServiceContainer
  {
    return $this->serviceContainer;
  }

  public function send(Response $response): void
  {
    if (headers_sent() === true) {
      throw new ResponseException('Response headers already sent.');
    }

    foreach ($response->getHeaders() as $name => $value) {
      header($name.': '.$value);
    }

    http_response_code($response->getStatus());
    print($response->getContent());
  }
}
