<template>
  <v-card flat
    class="mx-auto transparent"
    max-width="344"
    >
    <v-btn
      large
      @click="show = !show"
      text
      block
      >
      {{ trans('Click here for more details') }}
      <v-icon right>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
    </v-btn>

    <v-expand-transition>
      <div v-show="show">
        <v-divider></v-divider>
        <v-card>
          <v-card-text>
            <p>{{ __("I see you're signed in as superadmin - which is, like, super awesome!") }}</p>
            <p>{{ __("Here's some additional info just for you:") }}</p>
            <p><pre><code style="text-align:left;" class="php">text</code></pre></p>
            <p class="body-2 muted--text">{{ __('Other non-superadmins will not see this message.') }}</p>
          </v-card-text>
        </v-card>
      </div>
    </v-expand-transition>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    show: false,
  }),
}
</script>
