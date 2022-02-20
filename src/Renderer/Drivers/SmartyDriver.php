<?php

namespace Phoxx\Core\Renderer\Drivers;

use Smarty;
use Smarty_Security;

use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\Exceptions\RendererException;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;

class SmartyDriver implements Renderer
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
        $this->paths[$namespace][$path] = null;
        $this->security->secure_dir[] = $path;
    }

    public function render(View $view): string
    {
        // Resolve namespace
        preg_match('#^@([a-zA-Z-_]+)[\\\\/](.+)$#', $view->getTemplate(), $match);

        $namespace = $match[1] ?? null;
        $template = isset($match[2]) ? $match[2] . self::EXTENSION : $view->getTemplate() . self::EXTENSION;

        // Reject on absolute path or missing namespace
        if (preg_match('#^(?:[a-zA-Z]:[\\\\/]|/)#', $template) || !isset($this->paths[$namespace])) {
            throw new RendererException('Failed to match path for template `' . $template . '`.');
        }

        foreach (array_keys($this->paths[$namespace]) as $path) {
            // Resolve path
            $path = $path . '/' . $template;

            if ($this->smarty->templateExists($path)) {
                $resolved = $path;
                break;
            }
        }

        if (!isset($resolved)) {
            throw new FileException('Failed to resolve path for template `' . $template . '`.');
        }

        // Render template
        $this->smarty->assign($view->getParameters());

        $data = $this->smarty->fetch($resolved);

        $this->smarty->clearAllAssign();

        return $data;
    }
}
