<v-form action="{{ $action ?? null }}" method="POST">
  @csrf
  @isset($method) @method($method) @endisset
  {{ $slot }}
</v-form>
