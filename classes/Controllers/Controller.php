<?php

namespace Phoxx\Core\Controllers;

use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Framework\Interfaces\ServiceInterface;
use Phoxx\Core\Framework\Exceptions\ServiceException;

abstract class Controller
{
	private $serviceContainer;

	public function __construct(ServiceContainer $serviceContainer)
	{
		$this->serviceContainer = $serviceContainer;
	}

	public function getService(string $service): ?ServiceInterface
	{
		return $this->serviceContainer->getService($service);
	}

	public function getValue(string $index, $default)
	{
		if (isset($_POST[$index]) === true) {
			return $_POST[$index];
		}

		if (isset($_GET[$index]) === true) {
			return $_GET[$index];
		}

		return null;
	}

	public function getQuery(string $index, $default)
	{
		if (isset($_POST[$index]) === true) {
			return $_POST[$index];
		}

		return null;
	}

	public function getRequest(string $index, $default)
	{
		if (isset($_GET[$index]) === true) {
			return $_GET[$index];
		}

		return null;
	}

	public function redirect(string $uri): Response
	{
		return new Response($uri, array('Location' => $url));
	}
}