<?php

namespace Phoxx\Core\Renderer;

use Exception;

use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;

class Renderer
{
	const RENDERER_TWIG = 'twig';
	const RENDERER_SMARTY = 'smarty';
	
	protected static $instance;

	public static function core(): self
	{
		if (isset(static::$instance) === false) {
			$config = Config::core()->getFile('renderer');

			switch ((string)$config->RENDERER_DRIVER)
			{
				case self::RENDERER_TWIG:
					$driver = new TwigDriver((bool)$config->RENDERER_CACHE);
					break;

				case self::RENDERER_SMARTY:
					$driver = new SmartyDriver((bool)$config->RENDERER_CACHE);
					break;

				default:
					$driver = new PhpDriver();
					break;
			}
			static::$instance = new static($driver);
			static::$instance->addPath(PATH_CORE.'/views');
		}
		return static::$instance;
	}
	
	private $driver;

	public function __construct(RendererDriver $driver)
	{
		$this->driver = $driver;
	}

	protected function getDriver(): RendererDriver
	{
		return $this->driver;
	}

	public function addPath(string $path, string $namespace = null): void
	{
		$this->driver->addPath($path, $namespace);
	}

	public function render(View $view): string
	{
		return $this->driver->render($view);
	}
}