<v-list class="{{ $class ?? null }}" {{ $attr ?? null }}>
  <v-list-tile
    class="{{ $tile['class'] ?? null }}"
    href="{{ $url ?? null }}"
    value="{{ $value ?? null }}"
    {{ $tile['attr'] ?? null }}
    >
    @isset($avatar)
      <v-list-tile-action>
        {{ $avatar }}
      </v-list-tile-action>
    @endisset

    @isset($content)
      <v-list-tile-content>
        @isset($title)
          <v-list-tile-title>{{ $title }}</v-list-tile-title>
        @endisset
        @isset($subtitle)
          <v-list-tile-sub-title>{{ $subtitle }}</v-list-tile-sub-title>
        @endisset
      </v-list-tile-content>
    @endisset

    @isset($action)
      <v-list-tile-action>
        {{ $action }}
      </v-list-tile-action>
    @endisset
  </v-list-tile>
</v-list>
