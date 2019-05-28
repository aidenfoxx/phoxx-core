<?php

namespace Phoxx\Core\Renderer\Drivers;

use Twig_Environment;
use Twig_Loader_Filesystem;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;

class TwigDriver implements RendererDriver
{
	private $loader;

	private $twig;

	protected $extension = '.twig';

	public function __construct(bool $cache = true, string $base = PATH_BASE)
	{
		$this->loader = new Twig_Loader_Filesystem(array(), $base);
		$this->twig = new Twig_Environment($this->loader, array(
			'cache' => $cache === true ? PATH_CACHE.'/twig' : false
		));
	}

	public function getTwig(): Twig_Environment
	{
		return $this->twig;
	}
	
	public function addPath(string $path, string $namespace = null): void
	{
		$this->loader->addPath($path, $namespace !== null ? $namespace : Twig_Loader_Filesystem::MAIN_NAMESPACE);
	}

	public function render(View $view): string
	{
		return $this->twig->render($view->getTemplate().$this->extension, $view->getParameters());
	}
}