<template>
  <div>
    <v-slide-y-transition mode="out-in">
      <navbar>
        <slot name="navbar"></slot>
      </navbar>
    </v-slide-y-transition>

    <!-- # Main Content -->
    <v-main class="v-main-web">
      <div class="mb-12">
        <v-container class="container--web">
          <slot>
            <v-slide-y-reverse-transition mode="out-in">
              <router-view></router-view>
            </v-slide-y-reverse-transition>
          </slot>
        </v-container>
      </div>
      <v-footer absolute paddless color="workspace">
        <shortkey></shortkey>
        <v-container class="py-0 container--web">
          <v-row justify="end" align="center" no-gutters>
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
