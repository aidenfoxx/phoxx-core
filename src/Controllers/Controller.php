<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Framework\Dispatcher;
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Router\RouteContainer;

abstract class Controller extends Dispatcher
{
  private $serviceContainer;

  private $requestStack;

  public function __construct(RouteContainer $routeContainer, ServiceContainer $serviceContainer, RequestStack $requestStack)
  {
    parent::__construct($routeContainer, $serviceContainer, $requestStack);

    $this->serviceContainer = $serviceContainer;
    $this->requestStack = $requestStack;
  }

  public function getService(string $service)
  {
    return $this->serviceContainer->getService($service);
  }

  public function main(): ?Request
  {
    return $this->requestStack->main();
  }

  public function active(): ?Request
  {
    return $this->requestStack->active();
  }
}
