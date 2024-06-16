{{ $button ?? null }}
@push('after:footer')
  <div id="{{ $id ?? 'modal-'.time() }}" class="modal fade in" tabindex="-1" role="dialog">
    <form action="{{ $url ?? $action ?? null }}" method="post" autocomplete="off" class="modal-dialog" role="document" {{ ($upload ?? false) ? 'enctype=multipart/form-data' : null }}>
      @csrf
      <div class="modal-content bg-workspace">
        <div class="modal-header">
          @isset($title)<h5 class="modal-title">{{ $title }}</h5>@endisset
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        @isset($body)
          <div class="modal-body">
            {{ $body }}
          </div>
        @endisset

        {{ $slot }}

        <div class="modal-footer">
          @isset ($save)
            <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
            {{ $save }}
          @else
            <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Okay')</button>
          @endisset
        </div>
      </div>
    </form>
  </div>
@endpush
