<v-menu class="{{ $class ?? null }}" {{ $attr ?? null }}>
  @isset($items)
    @isset($activator)
      <template slot="activator">
        {{ $activator }}
      </template>
    @endisset

    {{ $items }}

  @endisset
</v-menu>
