<v-toolbar class="{{ $class ?? null }}" {{ $attr ?? null }}>
  @isset($title)
    <v-toolbar-title class="{{ $classTitle['class'] ?? null }}">
      {{ $title }}
    </v-toolbar-title>
  @endisset

  <v-spacer></v-spacer>

  @isset($divider)
    <v-divider vertical></v-divider>
  @endisset

  @isset($button)
    {{ $button }}
  @endisset

  {{ $slot }}
</v-toolbar>
