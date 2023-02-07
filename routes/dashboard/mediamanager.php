<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Orchid\Fields\Image;
use App\Orchid\Screens\MediaManager\MediaManagerScreen;
use App\Services\MediaManager\MediaManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;


Route::screen('media', MediaManagerScreen::class)->name('platform.mm');

Route::group(['prefix' => 'media-api'], function () {

  Route::get('/', function () {

    $tpl = request()->get('tpl') ?? '';
    $currentPath = request()->get('path') ?? '';
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
        'url'  => '',
      ],
    ];

    $counter = 1;
    foreach ($explode as $item) {
      $crumbs[] = [
        'text' => $item,
        'url'  => join('/', array_slice($explode, 0, $counter++)),
      ];
    }

    $vars = [
      'folders'     => $mm->getFolders(),
      'files'       => $mm->getFiles(),
      'currentPath' => $currentPath,
      'parentPath'  => $parentPath,
      'crumbs'      => $crumbs,
    ];

    return new JsonResponse([
      'content' => ($tpl && $tpl == 'modal')
        ? view('orchid.layouts.media-manager.media-manager-modal-content', $vars)->render()
        : view('orchid.layouts.media-manager.media-manager-content', $vars)->render(),
    ]);
  });

  Route::post('/delete', function (\Illuminate\Http\Request $request) {
    $inputPath = $request->get('path');
    $inputFile = $request->get('file');

    $path = trim(join('/', [$inputPath, $inputFile]), '/');

    $fs = \Illuminate\Support\Facades\Storage::disk('media');
    $deleted = false;
    if ($fs->has($path)) {
      if (File::isFile($fs->path($path))) {
        $deleted = $fs->delete($path);
      }
      if (File::isDirectory($fs->path($path))) {
        $deleted = $fs->deleteDirectory($path);
      }
    }

    return $deleted
      ? new JsonResponse(null, 201)
      : new JsonResponse([
        'message' => 'Not delete',
      ], 500);
  });

  Route::post('/upload', function (\Illuminate\Http\Request $request) {
    $path = trim($request->get('path') ?? '', '/\\');

    /** @var UploadedFile $uploadedFile */
    $uploadedFile = collect($request->allFiles())->flatten()->toArray()[0];
    $ext = $uploadedFile->getClientOriginalExtension();
    $filename = \Str::of($uploadedFile->getClientOriginalName())
      ->beforeLast($ext)
      ->slug()
      ->append('.'.$ext)->__toString();

    Storage::disk('media')->putFileAs(
      $path,
      $uploadedFile,
      $filename
    );

    return new JsonResponse([
      // 'uploadedFile' => $uploadedFile->getFilename(),
      // 'to'           => [$path, $filename],
    ]);
  });

  Route::post('/create', function (\Illuminate\Http\Request $request) {

    $fs = Storage::disk('media');
    $dir = $request->get('dir') ?? '';

    if (\Str::contains($dir, ['.', '/', '\\'])) {
      return new JsonResponse([
        'status'  => false,
        'message' => 'Invalid folder name',
      ]);
    }
    $path = collect([
      $request->get('path') ?? '',
      $dir,
    ])->filter()->map(function ($item) { return trim($item, '/\\'); })->join('/');


    $status = $fs->makeDirectory($path);

    return new JsonResponse([
      'status'  => $status,
      'message' => 'OK',
    ]);
  });

  Route::post('/add-image', function (\Illuminate\Http\Request $request) {
    $name = $request->get('name') ?? '';
    return new JsonResponse([
      'content' => Image::make($name)->__toString(),
    ]);
  });

  Route::post('/add-slide', function (\Illuminate\Http\Request $request) {
    $schema = $request->get('schema');
    $index = $request->get('index');
    $name = $request->get('name');

    $schema = json_decode(base64_decode($schema), true);
    $html = '<div class="shadow mx-n3 p-3 my-3 border-top field-slider__item">';
    $html .= '<button class="field-slider__delete" data-action="click->slider#delSlide">Del</button>';

    foreach ($schema as $field) {
      $html .= \App\Orchid\Fields\Slider::field($field, $name, $index);
    }

    $html .= '</div>';
    return $html;
  });

});
