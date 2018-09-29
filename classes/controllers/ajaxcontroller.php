<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Http\Response;

abstract class AjaxController extends BaseController
{
	public function display(array $data, int $status = Response::HTTP_OK): Response
	{
		return new Response(json_encode($data), $status, array(
			'Content-Type' => 'application/json'
		));
	}
}