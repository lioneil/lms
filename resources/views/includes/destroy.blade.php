@button([
  'icon' => 'mdi mdi-delete-outline',
  'text' => $button ?? null,
  'url' => null,
  'attr' => 'data-toggle=modal data-target=#modal-destroy-'.($id ?? $id = time())
])
@push('after:js')
  @modal(['id' => "modal-destroy-$id"])
    @slot('title')@lang('Move to Trash')@endslot

    <div class="row">
      <div class="col-md-9 mx-auto">
        @svg('theme::icons.move-to-trash', ['context' => 'warning'])
        <div class="text-center">
          <p class="lead">@lang('The selected resource will be moved to trash.')</p>
          <p>@lang('Are you sure you want to continue with the action?')</p>
        </div>
      </div>
    </div>

    @slot('save')
      <form action="{{ $url }}" method="POST">
        @csrf
        @method('DELETE')
        @submit($button, ['context' => 'warning'])
      </form>
    @endslot
  @endmodal
@endpush
