<?php

namespace Phoxx\Core\Renderer\Drivers;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Interfaces\RendererDriver;
use Phoxx\Core\Renderer\Exceptions\RendererException;
use Phoxx\Core\File\Exceptions\FileException;

class PhpDriver implements RendererDriver
{
  private const EXTENSION = '.php';

  protected $base;

  protected $paths = [];

  public function __construct(string $base = PATH_BASE)
  {
    $this->base = $base;
  }

  public function addPath(string $path, ?string $namespace = null): void
  {
    $this->paths[$namespace][$path] = true;
  }

  public function render(View $view): string
  {
    /**
     * Resolve namespace.
     */
    preg_match('#^@([a-zA-Z-_]+)[\\\\/](.+)$#', $view->getTemplate(), $match);

    $namespace = isset($match[1]) === true ? $match[1] : null;
    $template =  isset($match[2]) === true ? $match[2] . self::EXTENSION : $view->getTemplate() . self::EXTENSION;

    if ((bool)preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $template) === true || isset($this->paths[$namespace]) === false) {
      throw new RendererException('Failed to find path for template `' . $template . '`.');
    }

    foreach (array_keys($this->paths[$namespace]) as $path) {
      /**
       * Resolve relative path.
       */
      $path = $path . '/' . $config;
      $path = (bool)preg_match('#^(?:[a-zA-Z]:(?:\\\\|/)|/)#', $path) === false ? $this->base . '/' . $path : $path;

      if (($path = realpath($path)) !== false) {
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
    $parameters = $view->getParameters();

    foreach ($parameters as &$parameter) {
      $parameter = htmlspecialchars($parameter, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    extract($parameters);
    ob_start();

    include $resolved;

    return ob_get_clean();
  }
}
