<?php

namespace Phoxx\Core\System;

class Services
{
  protected $services = [];

  public function getService(string $className): ?object
  {
    return isset($this->services[$className]) ? $this->services[$className] : null;
  }

  public function setService(object $service, ?string $className = null): void
  {
    $this->services[$className ? $className : get_class($service)] = $service;
  }
}
