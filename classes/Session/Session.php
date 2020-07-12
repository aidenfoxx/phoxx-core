<?php

namespace Phoxx\Core\Session;

use Phoxx\Core\Session\Interfaces\SessionDriver;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Session
{
  protected $driver;

  public function __construct(SessionDriver $driver)
  {
    $this->driver = $driver;
  }

  public function getDriver(): SessionDriver
  {
    return $this->driver;
  }

  public function getValue(string $index)
  {
    return $this->driver->getValue($index);
  }

  public function setValue(string $index, $value): void
  {
    $this->driver->setValue($index, $value);
  }

  public function removeValue(string $index): void
  {
    $this->driver->removeValue($index);
  }

  public function active(): bool
  {
    return $this->driver->active();
  }

  public function open(): void
  {
    $this->driver->open();
  }

  public function close(): void
  {
    $this->driver->close();
  }

  public function clear(): void
  {
    $this->driver->clear();
  }
}
