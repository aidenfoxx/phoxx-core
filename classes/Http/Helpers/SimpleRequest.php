<?php

namespace Phoxx\Core\Http\Helpers;

use Phoxx\Core\Http\Request;

class SimpleRequest extends Request
{
    public function __construct(string $uri, string $method = 'GET') {
        parent::__construct(
        	$uri,
            $method,
        	$_GET, 
        	$_POST, 
        	$_SERVER, 
        	$_COOKIE, 
        	$_FILES, 
        	file_get_contents('php://input')
        );
    }
}