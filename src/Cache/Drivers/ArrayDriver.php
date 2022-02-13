<?php declare(strict_types=1);

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Cache;

class ArrayDriver implements Cache
{
  protected $cache = [];

  public function getValue(string $index)
  {
    if (!isset($this->cache[$index])) {
      return null;
    }

    return ($lifetime = $this->cache[$index]['lifetime']) === 0 || $lifetime > time() ? $this->cache[$index]['value'] : null;
  }

  public function setValue(string $index, $value, int $lifetime = 0): void
  {
    $this->cache[$index] = [
      'value' => $value,
      'lifetime' => $lifetime !== 0 ? time() + $lifetime : $lifetime
    ];
  }

  public function removeValue(string $index): void
  {
    unset($this->cache[$index]);
  }

  public function clear(): void
  {
    $this->cache = [];
  }
}
