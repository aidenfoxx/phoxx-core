<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Dispatcher;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Router\RouteContainer;

abstract class Controller extends Dispatcher
{
  private $services;

  private $requestStack;

  public function __construct(
    RouteContainer $router,
    ServiceContainer $services,
    array $requestStack = []
  ) {
    parent::__construct($router, $services, $requestStack);

    $this->serviceContainer = $serviceContainer;
    $this->requestStack = $requestStack;
  }

  public function getService(string $service)
  {
    return $this->services->getService($service);
  }

  public function main(): ?Request
  {
    return ($request = reset($this->requests)) !== false ? $request : null;
  }

  public function active(): ?Request
  {
    return ($request = end($this->requests)) !== false ? $request : null;
  }
}
