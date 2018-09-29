<?php

namespace Phoxx\Core\Package;

use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Database\Doctrine;
use Phoxx\Core\Package\Exceptions\PackageException;

class Package
{
	protected static $instances = array();

	public static function getInstance($package): self
	{
		if (isset(static::$instances[$package]) === false) {
			static::$instances[$package] = new static($package);
		}
		return static::$instances[$package];
	}

	private $config;

	private $isActive = false;

	protected $package;

	public function __construct($package)
	{
		$this->package = $package;
	}

	public function execute(): self
	{
		if ($this->isActive === false) {
			if (file_exists(PATH_PACKAGES.'/'.$this->package.'/bootstrap.php') === false) {
				throw new PackageException('Failed to initialize package `'.$this->package.'`.');
			}

			/**
			 * Add package paths to core services.
			 */
			Renderer::core()->addPath(PATH_PACKAGES.'/'.$this->package.'/views', $this->package);
			Doctrine::core()->addPath(PATH_PACKAGES.'/'.$this->package.'/doctrine');

			include(PATH_PACKAGES.'/'.$this->package.'/bootstrap.php');

			$this->isActive = true;
		}
		return $this;
	}

	public function config(): Config
	{
		if (isset($this->config) === false) {
			$this->config = new Config(PATH_PACKAGES.'/'.$this->package.'/config');
		}
		return $this->config;
	}

	public function isActive(): bool
	{
		return $this->isActive;
	}
}