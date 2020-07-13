<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\Renderer;
use Phoxx\Core\Renderer\View;

class FrontController extends Controller
{
  public function display(View $view, int $status = Response::HTTP_OK, array $headers = [])
  {
    if (($renderer = $this->getService(Renderer::class)) === null) {
      throw new ServiceException('Failed to load service `' . Renderer::class . '`.');
    }

    return new Response($renderer->render($view), $status, $headers);
  }
}
