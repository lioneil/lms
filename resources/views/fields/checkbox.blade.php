<v-checkbox
  label="@lang($label ?? null)"
  name="{{ $name ?? null }}"
  value="{{ $value ?? old($name ?? null) }}"
  {{ $attr ?? null }}
  >
</v-checkbox>
