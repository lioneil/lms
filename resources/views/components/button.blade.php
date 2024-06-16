<v-btn
  class="{{ $class ?? 'primary' }}"
  href="{{ $href ?? null }}"
  title="@lang($title ?? $text ?? null)"
  type="{{ $type ?? 'button' }}"
  {{ $attr ?? null }}
  >
  @isset($prepend) {{ $prepend }} @endisset
  {{ $slot }}
  @isset($append) {{ $append }} @endisset
</v-btn>
