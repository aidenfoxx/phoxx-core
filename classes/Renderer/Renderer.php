<?php

namespace Phoxx\Core\Renderer;

use Phoxx\Core\Utilities\Config;
use Phoxx\Core\Renderer\Drivers\PhpDriver;
use Phoxx\Core\Renderer\Drivers\TwigDriver;
use Phoxx\Core\Renderer\Drivers\SmartyDriver;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;
use Phoxx\Core\Framework\Interfaces\ServiceProvider;

class Renderer implements ServiceProvider
{
	private $driver;

	public function __construct(RendererDriver $driver)
	{
		$this->driver = $driver;
	}

	public function getServiceName(): string
	{
		return 'renderer';
	}

	public function getDriver(): RendererDriver
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