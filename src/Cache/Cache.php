<?php

namespace Phoxx\Core\Cache;

interface Cache
{
  public function getValue(string $index);

  public function setValue(string $index, $value, int $lifetime = 0): void;

  public function removeValue(string $index): void;

  public function clear(): void;
}
