<v-card {{ $attr ?? null }} class="{{ $class ?? null }}">
  @isset($title)
    <v-card-title primary-title>
      <h2 class="font-weight-bold mb-0">{{ $title }}</h2>
    </v-card-title>
  @endisset
  @isset($body)<v-card-text>{{ $body }}</v-card-text>@endisset
  {{ $slot }}
  @isset($footer)
    <v-card-actions class="{{ $actions['class'] ?? null }}">
      {{ $footer }}
    </v-card-actions>
  @endisset
</v-card>
