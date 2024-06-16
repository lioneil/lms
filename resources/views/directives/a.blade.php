<a
  class="{{ $class ?? null }}"
  href="{{ $url ?? null }}"
  title="@lang($title ?? $text ?? null)"
  {{ $attr ?? null }}
  >
  @isset($prepend)@icon($prepend) &nbsp;@endisset
  @lang($param ?? null)
  @isset($append)&nbsp;@icon($append)@endisset
</a>
