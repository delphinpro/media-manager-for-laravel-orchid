<?php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ ?>

@component($typeForm, get_defined_vars())
  <div class="field-image" data-controller="image">
    <div class="field-image__preview">
      <img class="field-image__img image" data-image-target="preview" src="{{ $src }}" alt="">
    </div>
    <div class="field-image__main">
      <div class="form-group">
        <div class="input-group">
          <input {{ $attributes->class(['font-monospace']) }} readonly data-image-target="filename">
          <button class="btn btn-dark" type="button" data-action="click->image#browse">Browse...</button>
        </div>
      </div>
      <div class="form-group mb-0">
        <div class="row">
          <div class="col">
            <div class="btn-group">
              <a class="btn btn-outline-secondary"
                href="{{ $attributes->get('value') }}"
                target="_blank"
                data-image-target="imageLink"
              >Open</a>
              <button class="btn btn-outline-danger ms-1" type="button" data-action="click->image#clear">Clear</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endcomponent
