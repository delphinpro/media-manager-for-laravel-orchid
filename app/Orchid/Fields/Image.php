<?php
/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

namespace App\Orchid\Fields;

use Orchid\Screen\Field;

/**
 * Class Image
 *
 * @package App\Orchid\Fields
 * @method Image title(string $value = null)
 */
class Image extends Field
{
  protected $view = 'orchid.fields.image';

  protected $attributes = [
    'class' => 'form-control',
    'src'   => '',
  ];

  protected $inlineAttributes = [
    'disabled',
    'name',
    'placeholder',
    'readonly',
    'required',
    // 'src',
    'tabindex',
    'type',
    'value',
  ];

  public function runBeforeRender(): Field
  {
    $this->set('src', $this->get('value') ?? '/icons/no-image.png');

    return parent::runBeforeRender();
  }
}
