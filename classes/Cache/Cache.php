<?php

namespace Phoxx\Core\Cache;

use Phoxx\Core\Cache\Interfaces\CacheDriver;

class Cache
{
  private $driver;

  public function __construct(CacheDriver $driver)
  {
    $this->driver = $driver;
  }

  public function getDriver(): CacheDriver
  {
    return $this->driver;
  }

  public function getValue(string $index)
  {
    return $this->driver->getValue($index);
  }

  public function setValue(string $index, $value, int $lifetime = 0): void
  {
    $this->driver->setValue($index, $value, $lifetime);
  }

  public function removeValue(string $index): void
  {
    $this->driver->removeValue($index);
  }

  public function clear(): void
  {
    $this->driver->clear();
  }
}
