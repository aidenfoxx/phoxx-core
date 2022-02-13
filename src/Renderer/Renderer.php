<?php

namespace Phoxx\Core\Renderer;

use Phoxx\Core\Renderer\View;

interface Renderer
{
  public function addPath(string $path, string $namespace = ''): void;

  public function render(View $view): string;
}
