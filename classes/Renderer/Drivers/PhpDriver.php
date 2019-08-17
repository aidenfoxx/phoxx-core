<?php

namespace Phoxx\Core\Renderer\Drivers;

use Exception;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\File\Exceptions\FileException;

class PhpDriver implements RendererDriver
{
	protected $base;

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

		if (isset($this->paths[$namespace]) === false) {
			throw new RendererException('Invalid namespace for template `'.$template.'`.');
		}

		foreach (array_keys($this->paths[$namespace]) as $path) {
			/**
			 * Resolve namespace.
			 */
			$path = $path.'/'.($namespace !== null ? substr($template, strlen($match[0])) : $template);

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
			throw new FileException('Failed to resolve path for template `'.$template.'`.');
		}

		/**
		 * Render template.
		 */
		$parameters = $view->getParameters();

		foreach ($parameters as &$parameter) {
			$parameter = htmlspecialchars($parameter, ENT_QUOTES, 'UTF-8');
		}

		extract($parameters);
		ob_start();

		include $resolved;

		return ob_get_clean();
	}
}