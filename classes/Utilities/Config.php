<?php

namespace Phoxx\Core\Utilities;

use stdClass;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Utilities\Exceptions\ConfigException;
use Phoxx\Core\File\Exceptions\FileException;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Config implements ServiceProvider
{
	private $cache;

	protected $base;

	protected $paths = array();	

	public function __construct(?Cache $cache = null, string $base = PATH_BASE)
	{
		$this->cache = $cache;
		$this->base = $base;
	}

	public function getServiceName(): string
	{
		return 'config';
	}

	public function addPath(string $path, string $namespace = null): void
	{
		$this->paths[$namespace][$path] = true;
	}

	public function getFile(string $file): ?stdClass
	{
		$namespace = (bool)preg_match('#^@([a-zA-Z-_]+)/#', $file, $match) === true ? $match[1] : null;

		if (isset($this->paths[$namespace]) === false) {
			throw new ConfigException('Invalid namespace for file `'.$file.'`.');
		}

		foreach (array_keys($this->paths[$namespace]) as $base) {
			/**
			 * Resolve namespace.
			 */
			$path = $base.'/'.($namespace !== null ? substr($file, strlen($match[0])) : $file).'.php';

			/**
			 * Resolve relative path.
			 */
			$path = (bool)preg_match('#^(?:[a-zA-Z]:(?:\\\\|/)|/)#', $path) === true ? $path : $this->base.'/'.$path;

			if (($path = realpath($path)) !== false) {
				$resolved = $path;
				break;
			}
		}

		if (isset($resolved) === false) {
			throw new FileException('Failed to resolve path for file `'.$file.'`.');
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