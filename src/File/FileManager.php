<?php

namespace Phoxx\Core\File;

use Phoxx\Core\File\FileExceptions\FileException;

class FileManager
{
  public function copy(File $file, string $dest): File
  {
    if (@copy($file->getPath(), $dest) === false) {
      throw new FileException('Failed to copy file to destination `' . $dest . '`.');
    }

    return new File($dest);
  }

  public function move(File $file, string $dest): File
  {
    if (@rename($file->getPath(), $dest) === false) {
      throw new FileException('Failed to move file to destination `' . $dest . '`.');
    }

    return new File($dest);
  }

  public function delete(File $file): void
  {
    $path = $file->getPath();

    if (@unlink($path) === false) {
      throw new FileException('Failed to remove file `' . $path . '`.');
    }
  }
}
