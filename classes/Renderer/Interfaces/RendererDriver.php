<?php

namespace Phoxx\Core\Renderer\Interfaces;

use Phoxx\Core\Renderer\View;

interface RendererDriver
{
	public function addPath(string $path, string $namespace = ''): void;
	
	public function render(View $view): string;
}