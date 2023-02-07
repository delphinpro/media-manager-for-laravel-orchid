<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

namespace App\Orchid\Screens\MediaManager;

use App\Services\MediaManager\MediaManager;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class MediaManagerScreen extends Screen
{
  public $name = 'Media Manager';

  public $description = '';

  public function query(): array
  {
    $currentPath = request()->get('path', '');
    platform404(\Str::contains($currentPath, ['..', '//', '\\']));

    $mm = new MediaManager($currentPath ?? '');
    $explode = array_filter(explode('/', trim($currentPath, '/')));

    if ($currentPath) {
      $parentPath = join('/', array_slice($explode, 0, count($explode) - 1));
    } else {
      $parentPath = '';
    }

    $crumbs = [
      [
        'text' => 'storage/media',
        'url'  => route('platform.mm').'?path=',//.$parentPath,
      ],
    ];

    $counter = 1;
    foreach ($explode as $item) {
      $crumbs[] = [
        'text' => $item,
        'url'  => route('platform.mm').'?path='.
          join('/', array_slice($explode, 0, $counter++)),
      ];
    }

    return [
      'folders'     => $mm->getFolders(),
      'files'       => $mm->getFiles(),
      'currentPath' => $currentPath,
      'parentPath'  => $parentPath,
      'crumbs'      => $crumbs,
    ];
  }

  public function commandBar(): array
  {
    return [];
  }

  public function layout(): array
  {
    return [
      Layout::view('orchid.layouts.media-manager.media-manager'),
    ];
  }
}
