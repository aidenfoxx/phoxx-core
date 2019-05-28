<?php

namespace Phoxx\Core\Controllers\Traits;

use Phoxx\Core\Framework\Exceptions\ServiceException;

trait HelperController
{
	public function getCsrf(): string
	{
		if (($session = $this->getService('session')) === null) {
			throw new ServiceException('Cannot find `session` service.');
		}

		/**
		 * TODO: Implement.
		 */
	}

	public function checkCsrf(string $csrf): void
	{
		if (($session = $this->getService('session')) === null) {
			throw new ServiceException('Cannot find `session` service.');
		}

		/**
		 * TODO: Implement.
		 */
	}
}