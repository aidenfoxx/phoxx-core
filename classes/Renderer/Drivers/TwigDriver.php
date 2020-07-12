<?php

namespace Phoxx\Core\Renderer\Drivers;

use Twig_Environment;
use Twig_Loader_Filesystem;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;

class TwigDriver implements RendererDriver
{
	private static $extension = '.twig';

	private $loader;

	private $twig;

	public function __construct(bool $cache = true, string $base = PATH_BASE)
	{
		$this->loader = new Twig_Loader_Filesystem([], $base);
		$this->twig = new Twig_Environment($this->loader, [
			'cache' => $cache === true ? PATH_CACHE.'/twig' : false
		]);
	}

	public function getTwig(): Twig_Environment
	{
		return $this->twig;
	}

	public function addPath(string $path, ?string $namespace = null): void
	{
		$this->loader->addPath($path, $namespace !== null ? $namespace : Twig_Loader_Filesystem::MAIN_NAMESPACE);
	}

	public function render(View $view): string
	{
		return $this->twig->render($view->getTemplate().self::$extension, $view->getParameters());
	}
}
