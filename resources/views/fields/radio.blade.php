<v-radio-group {{ $class ?? null }} {{ $attr ?? null }}>
  <v-radio
    class="{{ $radio['class'] ?? null }}"
    label="{{ $label ?? null }}"
    name="{{ $name ?? null }}"
    value="{{ old($name ?? null, $value ?? null) }}"
    {{ $attr ?? null }}
    >
  </v-radio>
</v-radio-group>
