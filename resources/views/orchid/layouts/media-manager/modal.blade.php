@once
  @push('modals-container')
    <div class="modal fade in {{$type}} mm-modal"
      id="screen-modal-{{$key}}"
      role="dialog"
      tabindex="-1"
      data-controller="mm-modal"
      {{$staticBackdrop ? "data-bs-backdrop=static" : ''}}
    >
      <div class="modal-dialog {{$size}}" role="document" id="screen-modal-type-{{$key}}">
        <div class="modal-content">
          <div class="modal-header mb-4">
            <h4 class="modal-title text-black fw-light">{{$title}}</h4>
            <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body layout-wrapper mm-modal__body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            <button type="button"
              class="btn btn-success"
              data-mm-modal-target="btnApply"
              data-action="click->mm-modal#applySelect"
              disabled
            >Select</button>
          </div>
        </div>
      </div>
    </div>
  @endpush
@endonce
