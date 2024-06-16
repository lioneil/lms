{{ $button ?? null }}
@push('after:footer')
  @modal(['id' => $id, 'url' => $url, 'class' => 'bg-workspace'])
    @slot('title')
      @isset($title)
        {{ $title }}
      @else
        @lang('Refresh')
      @endisset
    @endslot

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-10 mx-auto">
          @svg('theme::icons.permission', ['context' => $context ?? 'success', 'height' => '200px', 'class' => 'mb-3'])
          {{ $text ?? null }}
        </div>
      </div>
    </div>

    {{ $slot }}

    @slot('save')
      @isset($submit)
        {{ $submit }}
      @else
        @submit('Refresh', ['context' => 'primary'])
      @endisset
    @endslot
  @endmodal
@endpush
