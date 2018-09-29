<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Renderer\View;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Http\Response;

abstract class FrontController extends BaseController
{
	public function display(View $view, int $status = Response::HTTP_OK): Response
	{
		return new Response(Renderer::core()->render($view), $status);
	}
}