<template>
  <div>
    <v-slide-y-transition mode="out-in">
      <playerbar>
        <slot name="playerbar"></slot>
      </playerbar>
    </v-slide-y-transition>

    <!-- # Main Content -->
    <v-main>
      <div class="mb-12">
        <v-container class="container--player">
          <slot>
            <v-slide-y-reverse-transition mode="out-in">
              <router-view></router-view>
            </v-slide-y-reverse-transition>
          </slot>
          <slot name="playlist"></slot>
        </v-container>
      </div>
      <v-footer absolute paddless color="workspace">
        <shortkey></shortkey>
        <v-container class="py-0 container--player">
          <v-row justify="space-between" align="center" no-gutters>
            <v-col>
              <breadcrumbs></breadcrumbs>
            </v-col>
            <v-col cols="auto">
              <span class="overline link--text">{{ app.author }} &copy; {{ app.year }}</span>
            </v-col>
          </v-row>
        </v-container>
      </v-footer>
    </v-main>
    <!-- # Main Content -->

    <!-- # For Action Buttons -->
    <dialogbox></dialogbox>
    <snackbar></snackbar>
    <!-- # For Action Buttons -->
  </div>
</template>


<script>
import app from '@/config/app'

export default {
  computed: {
    app: function () {
      return app
    },
  }
}
</script>
