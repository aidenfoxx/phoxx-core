<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Http\Response;

class JsonController extends Controller
{
  public function display(array $data, int $status = Response::HTTP_OK, array $headers = []): Response
  {
    return new Response(json_encode($data), $status, array_merge($headers, ['Content-Type' => 'application/json']));
  }
}
