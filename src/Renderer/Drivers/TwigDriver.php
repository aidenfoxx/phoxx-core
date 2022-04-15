<?php

namespace Phoxx\Core\Renderer\Drivers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;

class TwigDriver implements Renderer
{
    private const EXTENSION = '.twig';

    private $loader;

    private $twig;

    public function __construct(bool $cache = true, string $base = PATH_BASE)
    {
        $this->loader = new FilesystemLoader([], $base);
        $this->twig = new Environment($this->loader, [
            'cache' => $cache ? PATH_CACHE . '/twig' : false
        ]);
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function addPath(string $path, ?string $namespace = null): void
    {
        $this->loader->addPath($path, $namespace ? $namespace : FilesystemLoader::MAIN_NAMESPACE);
    }

    public function render(View $view): string
    {
        return $this->twig->render($view->getTemplate() . self::EXTENSION, $view->getParameters());
    }
}
