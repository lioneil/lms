<v-btn
  type="submit"
  class="{{ $class ?? 'btn' }}"
  {{ $attr ?? null }}
  >
  @isset($prepend) {{ $prepend }} @endisset
  @lang($param ?? 'Submit')
  @isset($append) {{ $append }} @endisset
</v-btn>
