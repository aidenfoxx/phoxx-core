<?php

namespace Phoxx\Core\Controllers\Traits;

use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\View;
use Phoxx\Core\Framework\Exceptions\ServiceException;

trait DisplayController
{
	public function display(View $view, int $status = Response::HTTP_OK, array $headers = array()): Response
	{
		if (($renderer = $this->getService('renderer')) === false) {
			throw new ServiceException('Cannot find `renderer` service.');
		}

		return new Response($renderer->render($view), $status, $headers);
	}
}