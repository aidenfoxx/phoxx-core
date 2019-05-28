<?php

namespace Phoxx\Core\Controllers\Traits;

use Phoxx\Core\Http\Response;

trait AjaxController
{
	public function ajax(array $data, int $status = Response::HTTP_OK, array $headers = array()): Response
	{
		return new Response(json_encode($data), $status, array_merge($headers, array('Content-Type' => 'application/json')));
	}
}