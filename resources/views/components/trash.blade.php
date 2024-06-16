{{ $button ?? null }}
@push('after:footer')
  @modal(['id' => $id, 'url' => $url, 'class' => 'bg-workspace'])
    @method('delete')
    @slot('title')
      @isset($title)
        {{ $title }}
      @else
        @lang('Move to Trash')
      @endisset
    @endslot

    <div class="row">
      <div class="col-md-10 mx-auto">
        @svg('theme::icons.deactivate', ['context' => 'warning', 'height' => '200px', 'class' => 'mb-3'])
        {{ $slot }}
      </div>
    </div>

    @slot('save')
      @isset($submit)
        {{ $submit }}
      @else
        @submit('Move to Trash', ['context' => 'warning'])
      @endisset
    @endslot
  @endmodal
@endpush
