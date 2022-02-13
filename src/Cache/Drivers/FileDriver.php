<?php

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Cache;
use Phoxx\Core\Exceptions\FileException;

class FileDriver implements Cache
{
  protected $path;

  public function __construct(string $path = PATH_CACHE . '/default')
  {
    $this->path = $path;
  }

  protected function generatePath(string $index)
  {
    return $this->path . '/' . implode(str_split(md5($index), 12), '/');
  }

  public function getValue(string $index)
  {
    $path = $this->generatePath($index);

    if (!is_file($path)) {
      return null;
    }

    if (!($file = @fopen($path, 'r'))) {
      throw new FileException('Failed to open file `' . $path . '`.');
    }

    if (($line = (int)fgets($file)) !== 0 && $line < time()) {
      fclose($file);
      return null;
    }

    // Load and parse file
    $value = '';

    while (($line = fgets($file)) !== false) {
      $value .= $line;
    }

    fclose($file);

    return ($output = @unserialize($value)) !== false || $value === 'b:0;' ? $output : null;
  }

  public function setValue(string $index, $value, int $lifetime = 0): void
  {
    $path = $this->generatePath($index);
    $lifetime = $lifetime !== 0 ? time() + $lifetime : $lifetime;

    // Create dir if none exists.
    $directory = pathinfo($path, PATHINFO_DIRNAME);

    if (!is_dir($directory)) {
      @mkdir($directory, 0777, true);
    }

    if (!file_put_contents($path, $lifetime . PHP_EOL . serialize($value))) {
      throw new FileException('Failed to write file `' . $path . '`.');
    }
  }

  public function removeValue(string $index): void
  {
    $path = $this->generatePath($index);

    if (is_file($path) && !@unlink($path)) {
      throw new FileException('Failed to remove file `' . $path . '`.');
    }
  }

  public function clear(): void
  {
    if (is_dir($this->path)&& !@unlink($this->path)) {
      throw new FileException('Failed to remove directory `' . $this->path . '`.');
    }
  }
}
