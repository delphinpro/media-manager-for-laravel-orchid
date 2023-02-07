<?php /** @var \App\Services\MediaManager\FileSystemObject[] $folders */ ?>
<?php /** @var \App\Services\MediaManager\FileSystemObject[] $files */ ?>

<div class="mm-modal-content">
  <div class="mm-modal-content__bar">
    <div class="btn-group">
      <button class="btn btn-primary"
        @if(!$currentPath) disabled @endif
        data-action="click->mm-modal#goto"
        data-path-to="{{ $parentPath }}"
      >Up</button>
    </div>
    <div class="mm-modal-content__path">
      @foreach($crumbs as $crumb)
        @if(!$loop->last)
          <a href="{{ $crumb['url'] }}"
            data-action="click->mm-modal#goto"
            data-path-to="{{ $crumb['url'] }}"
          >{{ $crumb['text'] }}</a>
          /
        @else
          <span>{{ $crumb['text'] }}</span>
        @endif
      @endforeach
    </div>
  </div>

  <div class="mm-modal-content__list">
    @foreach([$folders, $files] as $group)
      <?php /** @var \App\Services\MediaManager\FileSystemObject $object */ ?>
      @foreach($group as $object)
        <div class="mm-modal-content__object mm-object"
          data-action="click->mm-modal#markObject dblclick->mm-modal#selectObject"
          data-mm-modal-target="fsObject"
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
          </div>
        </div>
      @endforeach
    @endforeach
  </div>
</div>
