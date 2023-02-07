<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

namespace App\Orchid\Layouts;

use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

class MediaManagerModalLayout extends Layout
{
  protected $template = 'orchid.layouts.media-manager.modal';

  public function __construct()
  {
    $this->variables = [
      'type'           => '',
      'key'            => 'media-manager',
      'staticBackdrop' => true,
      'size'           => 'modal-xl',
      'title'          => 'Media Manager: Select image',
    ];
  }

  /**
   * @param  Repository  $repository
   * @return mixed
   */
  public function build(Repository $repository)
  {
    return $this->buildAsDeep($repository);
  }
}
