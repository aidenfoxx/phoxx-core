<?php

namespace Phoxx\Core\System;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Exceptions\ConfigException;
use Phoxx\Core\Exceptions\FileException;

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

  public function open(string $config): ?object
  {
    // Resolve namespace
    preg_match('#^@([a-zA-Z-_]+)[\\\\/](.+)$#', $config, $match);

    $namespace = isset($match[1]) ? $match[1] : null;
    $config = isset($match[2]) ? $match[2] . self::EXTENSION : $config . self::EXTENSION;

    // Reject on absolute path or missing namespace
    if (preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $config) || !isset($this->paths[$namespace])) {
      throw new ConfigException('Failed to find path for file `' . $config . '`.');
    }

    foreach (array_keys($this->paths[$namespace]) as $path) {
      // Resolve relative paths
      $path = $path . '/' . $config;
      $path = !preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $path) ? $this->base . '/' . $path : $path;

      if ($path = realpath($path)) {
        $resolved = $path;
        break;
      }
    }

    if (!isset($resolved)) {
      throw new FileException('Failed to resolve path for file `' . $config . '`.');
    }

    // Check for config in cache
    if ($this->cache && $config = $this->cache->getValue($resolved)) {
      return (object)$config;
    }

    $config = include $resolved;

    if ($this->cache) {
      $this->cache->setValue($resolved, $config);
    }

    return (object)$config;
  }
}
