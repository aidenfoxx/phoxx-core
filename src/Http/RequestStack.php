<?php

namespace Phoxx\Core\Http;

use Phoxx\Core\Http\Request;

class RequestStack
{
  protected $requests = [];

  public function push(Request $request): void
  {
    $this->requests[] = $request;
  }

  public function pop(): ?Request
  {
    return array_pop($this->requests);
  }

  public function main(): ?Request
  {
    return ($request = reset($this->requests)) !== false ? $request : null;
  }

  public function active(): ?Request
  {
    return ($request = end($this->requests)) !== false ? $request : null;
  }
}
