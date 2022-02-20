<?php

namespace Phoxx\Core\Renderer\Drivers;

use Phoxx\Core\Exceptions\RendererException;
use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;

class PhpDriver implements Renderer
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
        $this->paths[$namespace][$path] = null;
    }

    public function render(View $view): string
    {
        // Resolve namespace
        preg_match('#^@([a-zA-Z-_]+)[\\\\/](.+)$#', $view->getTemplate(), $match);

        $namespace = $match[1] ?? null;
        $template =    isset($match[2]) ? $match[2] . self::EXTENSION : $view->getTemplate() . self::EXTENSION;

        // Reject on absolute path or missing namespace
        if (preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $template) || !isset($this->paths[$namespace])) {
            throw new RendererException('Failed to match path for template `' . $template . '`.');
        }

        foreach (array_keys($this->paths[$namespace]) as $path) {
            // Resolve relative paths
            $path = $path . '/' . $template;
            $path = !preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $path) ? $this->base . '/' . $path : $path;

            if ($path = realpath($path)) {
                $resolved = $path;
                break;
            }
        }

        if (!isset($resolved)) {
            throw new FileException('Failed to resolve path for template `' . $template . '`.');
        }

        // Render template
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
