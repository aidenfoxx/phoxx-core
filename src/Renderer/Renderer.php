<?php

namespace Phoxx\Core\Renderer;

interface Renderer
{
  public function addPath(string $path, string $namespace = ''): void;

  public function render(View $view): string;
}
