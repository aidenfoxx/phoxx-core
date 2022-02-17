<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Router;
use Phoxx\Core\System\Services;

abstract class Controller
{
  private $router;

  private $services;

  public function __construct(Router $router, Services $services) {
    $this->router = $router;
    $this->services = $services;
  }

  public function getService(string $service)
  {
    return $this->services->getService($service);
  }

  public function dispatch(Request $request): ?Response
  {
    return $this->router->dispatch($request);
  }

  public function main(): ?Request
  {
    return $this->router->main();
  }

  public function active(): ?Request
  {
    return $this->router->active();
  }
}
