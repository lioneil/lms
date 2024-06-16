<v-text-field
  :error-messages="{{ json_encode($errors->get($name ?? 'text')) }}"
  append-icon="{{ $appendIcon ?? null }}"
  append-inner-icon="{{ $prependInnerIcon ?? null }}"
  box
  class="{{ $class ?? null }}"
  label="@lang($label ?? null)"
  min="{{ $min ?? null }}"
  name="{{ $name ?? null }}"
  prepend-icon="{{ $prependIcon ?? null }}"
  prepend-inner-icon="{{ $prependInnerIcon ?? null }}"
  type="{{ $type ?? 'text' }}"
  value="{{ old($name ?? null, $value ?? null) }}"
  >
</v-text-field>
