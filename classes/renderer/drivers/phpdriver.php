<?php

namespace Phoxx\Core\Renderer\Drivers;

use Exception;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;

class PhpDriver implements RendererDriver
{
	private $base;

	protected $extension = '.php';

	protected $paths = array();	

	public function __construct(string $base = PATH_BASE)
	{
		$this->base = $base;
	}
	
	public function addPath(string $path, string $namespace = null): void
	{
		$this->paths[$namespace][$path] = true;
	}

	public function render(View $view): string
	{
		$template = $view->getTemplate().$this->extension;
		$namespace = (bool)preg_match('#^@([a-zA-Z-_]+)/#', $template, $match) === true ? $match[1] : null;

		if (isset($this->paths[$namespace]) === true) {
			foreach (array_keys($this->paths[$namespace]) as $path) {
				/**
				 * Resolve namespace and relative paths.
				 */
				$resolvedPath = $path.'/'.($namespace !== null ? substr($template, strlen($match[0])) : $template);
				$resolvedPath = (bool)preg_match('#^(?:[a-zA-Z]:(?:\\\\|/)|/)#', $resolvedPath) === true ? $resolvedPath : $this->base.'/'.$resolvedPath;

				if (file_exists($resolvedPath) === true) {
					$template = $resolvedPath;
					break;
				}
			}
		} else {
			throw new RendererException('Could not locate path for template `'.$view->getTemplate().'`.');
		}

		/**
		 * Escape parameters.
		 */
		$parameters = array();

		foreach ($view->getParameters() as $key => $value) {
			$parameters[$key] = htmlentities($value);
		}

		/**
		 * Bind parameters and render view.
		 */
		extract($parameters);
		ob_start();

		if (file_exists($template) === true) {
			include($template);
		} else {
			throw new RendererException('Could not render template `'.$view->getTemplate().'`.');
		}

		return ob_get_clean();
	}
}