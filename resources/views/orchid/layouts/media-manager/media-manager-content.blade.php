<?php
/**
 * @var \App\Services\MediaManager\FileSystemObject[] $folders
 * @var \App\Services\MediaManager\FileSystemObject[] $files
 */
?>

<div class="media-manager">
  <div class="media-manager__bar">
    <div class="btn-group">
      <button class="btn btn-primary"
        @if(!$currentPath) disabled @endif
      data-action="click->media-manager#goto"
        data-path-to="{{ $parentPath }}"
      >Up</button>
    </div>
    <div class="media-manager__path">
      @foreach($crumbs as $crumb)
        @if(!$loop->last)
          <a href="{{ $crumb['url'] }}"
            data-action="click->media-manager#goto"
            data-path-to="{{ $crumb['url'] }}"
          >{{ $crumb['text'] }}</a>
          /
        @else
          <span>{{ $crumb['text'] }}</span>
        @endif
      @endforeach
    </div>
    <div class="btn-group ms-auto">
      <label class="btn btn-success mb-0" for="media-manager-uploader">Upload</label>
      <button class="btn btn-primary" data-action="click->media-manager#dirToggle">Add Folder</button>
    </div>
    <div class="d-none">
      <input type="file" name="image" id="media-manager-uploader" data-action="change->media-manager#upload">
    </div>
  </div>
  <div class="media-manager__dir bg-white" data-media-manager-target="dir" hidden>
    <div class="input-group" style="max-width: none;">
      <input type="text"
        class="form-control"
        placeholder="Enter the folder name"
        data-media-manager-target="dirInput"
        data-action="input->media-manager#resetValidation"
      >
      <div class="input-group-append">
        <button class="btn btn-success" type="button" data-action="click->media-manager#createDir">Create</button>
      </div>
      <div class="input-group-append">
        <button class="btn btn-secondary" data-action="click->media-manager#dirToggle">Cancel</button>
      </div>
    </div>
      <div class="form-text">{{ __('validation.slug', ['attribute' => 'folder name']) }}</div>
  </div>

  <div class="media-manager__main">
    <div class="media-manager__list">
      @foreach([$folders, $files] as $group)
        <?php /** @var \App\Services\MediaManager\FileSystemObject $object */ ?>
        @foreach($group as $object)
          <div class="media-manager__object mm-object"
            data-action="click->media-manager#markObject dblclick->media-manager#selectObject"
            data-media-manager-target="fsObject"
            {{--            data-object='{{ $object->toJson() }}'--}}
            data-object-type="{{ $object->isDir ? 'dir' : 'file' }}"
            data-object-name="{{ $object->basename }}"
            data-object-filename="{{ $object->filename }}"
          >
            <div class="mm-object__preview">
              @if ($object->isDir)
                <x-orchid-icon class="mm-object__icon" path="icon.fas-folder" width="48" height="48"/>
              @endif
              @if($object->isFile && $object->isReadable)
                <img class="mm-object__img" src="{{ $object->url }}" alt="">
              @endif
            </div>
            <div class="mm-object__main">
              <div class="mm-object__name">{{ $object->filename }}</div>
              @if($object->isFile)
                <div class="mm-object__meta mm-object__size">{{ human_filesize($object->size) }}</div>
              @endif

              <div class="btn-group mm-object__menu">
                <button class="btn btn-link btn-sm dropdown-toggle dropdown-item p-2"
                  type="button"
                  data-bs-toggle="dropdown"
                >
                  <x-orchid-icon path="options-vertical"/>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow bg-white" style="">
                  {{--
                  <button class="dropdown-item">
                    <span class="col-auto me-auto p-0 v-center">
                      Rename
                    </span>
                  </button>
                  --}}
                  <button class="dropdown-item" data-action="click->media-manager#delete">
                    <span class="col-auto me-auto p-0 v-center">
                      Delete
                    </span>
                  </button>
                </div>
              </div>

            </div>
          </div>
        @endforeach
      @endforeach
    </div>
  </div>

</div>
