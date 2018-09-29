<?php

namespace Phoxx\Core\Utilities;

use stdClass;

class Config
{
	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			static::$instance = new static(PATH_CORE.'/config');
		}
		return static::$instance;
	}
	
	protected $path;

	protected $config = array();

	public function __construct(string $path)
	{
		$this->path = $path;
	}

	public function getFile(string $file): ?stdClass
	{
		if (isset($this->config[$file]) === false) {
			if (file_exists($this->path.'/'.$file.'.php') === true) {
				$this->config[$file] = include($this->path.'/'.$file.'.php');
			} else {
				return null;
			}
		}
		return (object)$this->config[$file];
	}
}