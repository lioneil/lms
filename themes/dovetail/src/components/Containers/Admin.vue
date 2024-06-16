<template>
  <div>
    <v-slide-y-transition mode="out-in">
      <appbar>
        <slot name="appbar"></slot>
      </appbar>
    </v-slide-y-transition>

    <!-- # Main Content -->
    <v-main style="min-height:100vh">
      <div class="mb-16 pb-5 mb-md-12">
        <v-container>
          <slot>
            <v-slide-y-reverse-transition mode="out-in">
              <router-view></router-view>
            </v-slide-y-reverse-transition>
          </slot>
        </v-container>
      </div>
      <v-footer absolute paddless color="workspace">
        <shortkey></shortkey>
        <v-container class="py-0">
          <v-row justify-md="space-between" align="center" no-gutters>
            <v-col cols="12" md="8">
              <breadcrumbs></breadcrumbs>
            </v-col>
            <v-col cols="12" md="4">
              <div class="text-md-right text-left">
                <span class="overline link--text">{{ app.author }} &copy; {{ app.year }}</span>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </v-footer>
    </v-main>
    <!-- # Main Content -->
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
