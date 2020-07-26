<?php

namespace Phoxx\Core\Utilities;

use stdClass;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Utilities\Exceptions\ConfigException;
use Phoxx\Core\File\Exceptions\FileException;

class Config
{
  private const EXTENSION = '.php';

  protected $cache;

  protected $base;

  protected $paths = [];

  public function __construct(?Cache $cache = null, string $base = PATH_BASE)
  {
    $this->cache = $cache;
    $this->base = $base;
  }

  public function addPath(string $path, ?string $namespace = null): void
  {
    $this->paths[$namespace][$path] = true;
  }

  public function getFile(string $config): ?stdClass
  {
    /**
     * Resolve namespace.
     */
    preg_match('#^@([a-zA-Z-_]+)[\\\\/](.+)$#', $config, $match);

    $namespace = isset($match[1]) === true ? $match[1] : null;
    $config =  isset($match[2]) === true ? $match[2] . self::EXTENSION : $config . self::EXTENSION;

    if ((bool)preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $config) === true || isset($this->paths[$namespace]) === false) {
      throw new ConfigException('Failed to find path for file `' . $config . '`.');
    }

    foreach (array_keys($this->paths[$namespace]) as $path) {
      /**
       * Resolve relative path.
       */
      $path = $path . '/' . $config;
      $path = (bool)preg_match('#^(?:[a-zA-Z]:(?:\\\\|/)|/)#', $path) === false ? $this->base . '/' . $path : $path;

      if (($path = realpath($path)) !== false) {
        $resolved = $path;
        break;
      }
    }

    if (isset($resolved) === false) {
      throw new FileException('Failed to resolve path for file `' . $config . '`.');
    }

    /**
     * Check for config in cache.
     */
    if ($this->cache !== null && ($config = $this->cache->getValue($resolved)) !== null) {
      return (object)$config;
    }

    $config = include $resolved;

    if ($this->cache !== null) {
      $this->cache->setValue($resolved, $config);
    }

    return (object)$config;
  }
}
