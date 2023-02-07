<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

namespace App\Services\MediaManager;

use Illuminate\Support\Facades\Storage;

/**
 * Class FileSystemObject
 *
 * @package App\Services\MediaManager
 * @property int $aTime
 * @property int $cTime
 * @property int $mTime
 * @property string $extension
 * @property string $basename
 * @property string $filename
 * @property string $path
 * @property string $pathname
 * @property int $group
 * @property int $iNode
 * @property int $owner
 * @property int $perms
 * @property int $size
 * @property string $type
 * @property bool $isDir
 * @property bool $isDot
 * @property bool $isExecutable
 * @property bool $isFile
 * @property bool $isLink
 * @property bool $isReadable
 * @property bool $isWritable
 * @property string $url
 */
class FileSystemObject
{
  private $attributes = [];

  private $directory;

  public function __construct(\DirectoryIterator $obj, string $directory)
  {
    $this->directory = $directory;

    $extension = $obj->getExtension();
    $filename = $obj->getFilename();

    $this->attributes = [

      'aTime' => $obj->getATime(),
      'cTime' => $obj->getCTime(),
      'mTime' => $obj->getMTime(),

      'extension' => $extension,
      'basename'  => $obj->getBasename('.'.$extension),
      'filename'  => $filename,
      'path'      => $obj->getPath(),
      'pathname'  => $obj->getPathname(),

      'group' => $obj->getGroup(),
      'iNode' => $obj->getInode(),
      'owner' => $obj->getOwner(),
      'perms' => $obj->getPerms(),
      'size'  => $obj->getSize(),
      'type'  => $obj->getType(),

      'isDir'        => $obj->isDir(),
      'isDot'        => $obj->isDot(),
      'isExecutable' => $obj->isExecutable(),
      'isFile'       => $obj->isFile(),
      'isLink'       => $obj->isLink(),
      'isReadable'   => $obj->isReadable(),
      'isWritable'   => $obj->isWritable(),

      'url' => Storage::disk('media')->url($this->directory.'/'.$filename),

    ];
  }

  public function __get($name)
  {
    return $this->attributes[$name] ?? null;
  }

  public function toJson()
  {
    return json_encode($this->attributes);
  }
}
