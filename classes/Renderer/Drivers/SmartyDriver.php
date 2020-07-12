<?php

namespace Phoxx\Core\Renderer\Drivers;

use Smarty;
use Smarty_Security;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\File\Exceptions\FileException;

class SmartyDriver implements RendererDriver
{
  private const EXTENSION = '.tpl';

  private $smarty;

  private $security;

  protected $paths = [];

  public function __construct(bool $cache = true, bool $forceCompile = false, string $base = PATH_BASE)
  {
    $this->smarty = new Smarty();
    $this->security = new Smarty_Security($this->smarty);

    $this->security->php_handling = Smarty::PHP_REMOVE;
    $this->security->static_classes = null;
    $this->security->streams = null;
    $this->security->allow_super_globals = false;

    $this->smarty->setTemplateDir($base);
    $this->smarty->setCompileDir(PATH_CACHE . '/smarty/templates_c');
    $this->smarty->setCacheDir(PATH_CACHE . '/smarty/cache');
    $this->smarty->setConfigDir(PATH_CACHE . '/smarty/configs');
    $this->smarty->enableSecurity($this->security);

    $this->smarty->caching = (int)$cache;
    $this->smarty->force_compile = $forceCompile;
    $this->smarty->escape_html = true;
  }

  public function getSmarty(): Smarty
  {
    return $this->smarty;
  }

  public function addPath(string $path, ?string $namespace = null): void
  {
    $this->paths[$namespace][$path] = true;
    $this->security->secure_dir[] = $path;
  }

  public function render(View $view): string
  {
    $template = $view->getTemplate() . self::EXTENSION;
    $namespace = (bool)preg_match('#^@([a-zA-Z-_]+)/#', $template, $match) === true ? $match[1] : null;

    if (isset($this->paths[$namespace]) === false) {
      throw new RendererException('Invalid namespace for template `' . $template . '`.');
    }

    foreach (array_keys($this->paths[$namespace]) as $path) {
      /**
       * Resolve namespace.
       */
      $path = $path . '/' . ($namespace !== null ? substr($template, strlen($match[0])) : $template);

      if ($this->smarty->templateExists($path) === true) {
        $resolved = $path;
        break;
      }
    }

    if (isset($resolved) === false) {
      throw new FileException('Failed to resolve path for template `' . $template . '`.');
    }

    /**
     * Render template.
     */
    $this->smarty->assign($view->getParameters());
    $data = $this->smarty->fetch($resolved);
    $this->smarty->clearAllAssign();

    return $data;
  }
}
