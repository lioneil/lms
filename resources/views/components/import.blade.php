{{ $button ?? null }}
@push('after:footer')
  @modal(['id' => $id, 'url' => $url, 'class' => 'bg-workspace'])
    @method('put')

    @slot('title')
      @isset($title)
        {{ $title }}
      @else
        @lang('Import')
      @endisset
    @endslot

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-10 mx-auto">
          @svg($svg ?? 'theme::icons.accept', ['context' => 'primary', 'height' => '200px', 'class' => 'mb-3'])
          @isset($text){{ $text }}@endisset
        </div>
      </div>
    </div>

    {{ $slot }}

    @slot('save')
      @isset($submit)
        {{ $submit }}
      @else
        @submit('Import', ['context' => 'primary'])
      @endisset
    @endslot
  @endmodal
@endpush
