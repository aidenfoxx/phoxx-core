<?php 

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Interfaces\CacheDriver;
use Phoxx\Core\File\Exceptions\FileException;

class FileDriver implements CacheDriver
{
	private $path;

	public function __construct(string $path = PATH_CACHE.'/default')
	{
		$this->path = $path;
	}

	protected function generatePath(string $index)
	{
		return $this->path.'/'.implode(str_split(md5($index), 12), '/');
	}

	public function getValue(string $index)
	{
		$path = $this->generatePath($index);

		if (is_file($path) === false) {
			return null;
		}

		$file = fopen($path, 'r');

		if (($line = fgets($file)) === false) {
			fclose($file);
			return null;
		}

		if ((int)$line !== 0 && (int)$line < time()) {
			fclose($file);
			return null;
		}

		/**
		 * Load and parse file.
		 */
		$value = '';

		while (($line = fgets($file)) !== false) {
			$value .= $line;
		}

		fclose($file);
			
		return ($value = @unserialize($value)) === false ? null : $value;
	}

	public function setValue(string $index, $value, int $lifetime = 0): void
	{
		$path = $this->generatePath($index);
		$lifetime = $lifetime !== 0 ? time() + $lifetime : $lifetime;

		/**
		 * Create dir if none exists.
		 */
		$directory = pathinfo($path, PATHINFO_DIRNAME);

		if (is_dir($directory) === false) {
			mkdir($directory, 0777, true);
			chmod($directory, 0777);
		}
		
		file_put_contents($path, $lifetime.PHP_EOL.serialize($value));
	}

	public function removeValue(string $index): void
	{
		$path = $this->generatePath($index);

		if (is_file($path) === true) {
			unlink($path);
		}
	}

	public function clear(): void
	{
		if (is_dir($this->path) === true) {
			unlink($this->path);
		}
	}
}

