<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class FileManager
{
  public function copy(File $file, string $dest): void
  {
    $path = $file->getPath();

    if (@copy($path, $dest) === false) {
      throw new FileException('Failed to copy file `' . $path . '` to destination `' . $dest . '`.');
    }
  }

  public function move(File $file, string $dest): void
  {
    $path = $file->getPath();

    if (@rename($path, $dest) === false) {
      throw new FileException('Failed to move file `' . $path . '` to destination `' . $dest . '`.');
    }
  }

  public function delete(File $file): void
  {
    $path = $file->getPath();

    if (@unlink($path) === false) {
      throw new FileException('Failed to remove file `' . $path . '`.');
    }
  }
}
