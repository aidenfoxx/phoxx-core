<?php

namespace Phoxx\Core\Renderer\Drivers;

use Smarty;
use Smarty_Security;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;

class SmartyDriver implements RendererDriver
{
	private $smarty;

	private $security;

	protected $extension = '.tpl';

	protected $paths = array();

	public function __construct(bool $cache = true, string $base = PATH_BASE)
	{
		$this->smarty = new Smarty();
		$this->security = new Smarty_Security($this->smarty);

		$this->security->php_handling = Smarty::PHP_REMOVE;
		$this->security->static_classes = null;
		$this->security->streams = null;
		$this->security->allow_super_globals = false;

		$this->smarty->setTemplateDir($base);
		$this->smarty->setCompileDir(PATH_CACHE.'/smarty/templates_c');
		$this->smarty->setCacheDir(PATH_CACHE.'/smarty/cache');
		$this->smarty->setConfigDir(PATH_CACHE.'/smarty/configs');
		$this->smarty->enableSecurity($this->security);

		$this->smarty->caching = (int)$cache;
		$this->smarty->escape_html = true;
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
				$resolvedPath = $path.'/'.($namespace !== null ? substr($template, strlen($match[0])) : $template);
				if ($this->smarty->templateExists($resolvedPath)) {
					$template = $resolvedPath;
					break;
				}
			}
		} else {
			throw new RendererException('Could not locate path for template `'.$view->getTemplate().'`.');
		}

		$this->smarty->assign($view->getParameters());
		$data = $this->smarty->fetch($template);
		$this->smarty->clearAllAssign();

		return $data;
	}
}