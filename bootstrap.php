<?php

use Phoxx\Core\Http\SimpleRequest;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\Dispatcher;
use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\Router;
use Phoxx\Core\Package\Package;
use Phoxx\Core\Utilities\Config;

$debug = (bool)Config::core()->getFile('core')->CORE_DEBUG;

/**
 * Check if dubugging is enabled and show whoops
 * errors if true.
 */
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', E_ALL); 

	$whoops = new Whoops\Run;
	$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
	$whoops->register();
} else {
	error_reporting(0);
	ini_set('display_errors', 0); 
}

/**
 * Load packages.
 */
foreach (Config::core()->getFile('packages') as $package) {
	Package::getInstance($package)->execute();
}

/**
 * Dispatch the default HTTP request into
 * the framework.
 */
try {
	$request = new SimpleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
	$response = Dispatcher::core()->dispatch($request);

	if ($response instanceof Response) {
		$response->send();
		exit;
	}

	/**
	 * Try to execute 404 page.
	 */
	if (($route = Router::core()->match('_404_')) instanceof Route) {
		if (($response = $route->execute()->getResponse()) instanceof Response) {
			$response->send();
			exit;
		}
	}
} catch (Exception $e) {
	/**
	 * If debugging is enabled throw the error.
	 */
	if ($debug === true) {
		throw $e;
	}

	/**
	 * Try to execute 500 page.
	 */
	if (($route = Router::core()->match('_500_')) instanceof Route) {
		if (($response = $route->execute()->getResponse()) instanceof Response) {
			$response->send();
			exit;
		}
	}
}