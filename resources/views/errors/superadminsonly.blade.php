@if (user() && user()->isSuperAdmin())
  <v-expansion-panel popout inset class="mt-5">
    <v-expansion-panel-content>
      <template v-slot:header>
        <div>{{ __('Psst, Hey!') }}</div>
      </template>
      @card
        @slot('body')
          <p>{{ __("I see you're signed in as ".user()->role." - which is, like, super awesome!") }}</p>
          <p>{{ __("Here's some additional info just for you:") }}</p>
          <pre><code class="php">{{ var_dump($error ?? []) }}</code></pre>
          <p class="muted--text">{{ __('Other non-superadmins will not see this message.') }}</p>
        @endslot
      @endcard
    </v-expansion-panel-content>
  </v-expansion-panel>
@endif
