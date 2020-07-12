<?php

namespace Phoxx\Core\Database\Doctrine;

use Doctrine\Common\Cache\CacheProvider;

use Phoxx\Core\Cache\Cache;

class CacheInterface extends CacheProvider
{
  private $cache;

  public function __construct(Cache $cache)
  {
    $this->cache = $cache;
  }

  protected function doFetch($key)
  {
    return $this->cache->getValue((string)$key);
  }

  protected function doContains($key)
  {
    return $this->cache->getValue((string)$key) !== null;
  }

  protected function doSave($key, $value, $lifetime = 0)
  {
    $this->cache->setValue((string)$key, $value, (int)$lifetime);
  }

  protected function doDelete($key)
  {
    $this->cache->removeValue((string)$key, $value);
  }

  protected function doFlush()
  {
    $this->cache->clear();
  }

  protected function doGetStats()
  {
    return null;
  }
}
