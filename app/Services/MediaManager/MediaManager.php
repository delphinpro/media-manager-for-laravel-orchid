<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

namespace App\Services\MediaManager;

use DirectoryIterator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Class MediaManager
 *
 * @package App\Services\MediaManager
 */
class MediaManager
{
  private $root = '';

  /** @var \Illuminate\Support\Collection */
  private $folders;

  /** @var \Illuminate\Support\Collection */
  private $files;

  public function __construct(string $path = '')
  {
    $path = trim($path, '/');
    $dir = new DirectoryIterator(Storage::disk('media')->path(join('/', array_filter([$this->root, $path]))));
    $this->folders = collect([]);
    $this->files = collect([]);
    /** @var \DirectoryIterator $item */
    foreach ($dir as $item) {
      if ($item->isDot()) continue;
      if ($item->isDir()) {
        $this->folders->push(new FileSystemObject($item, $path));
      }
      if ($item->isFile()) {
        $this->files->push(new FileSystemObject($item, $path));
      }
    }

    $this->folders = $this->folders->sort(function (FileSystemObject $a, FileSystemObject $b) {
      if ($a->basename == $b->basename) return 0;
      return ($a->basename > $b->basename) ? 1 : -1;
    });

    $this->files = $this->files->sort(function (FileSystemObject $a, FileSystemObject $b) {
      if ($a->basename == $b->basename) return 0;
      return ($a->basename > $b->basename) ? 1 : -1;
    });
  }

  public function getFolders(): Collection
  {
    return $this->folders;
  }

  public function getFiles(): Collection
  {
    return $this->files;
  }
}
