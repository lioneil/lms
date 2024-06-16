<v-img
  :aspect-ratio="{{ $ratio ?? '2' }}"
  gradient="{{ $gradient ?? null }}"
  src="{{ $src ?? null }}"
  {{ $attr ?? null }}
  >
  {{ $slot }}
</v-img>
