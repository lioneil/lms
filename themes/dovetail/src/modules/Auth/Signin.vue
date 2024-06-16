<template>
  <v-container fluid fill-height>
    <v-row justify="start" align="center">
      <v-col cols="12" md="10">
        <v-row justify="center" align="start">
          <v-col cols="12" md="7" class="d-none d-md-flex">
            <img width="100%" src="@/components/Illustration/mobile.svg" alt="">
          </v-col>

          <v-col cols="12" sm="10" md="4" offset-md="1">
            <v-fade-transition mode="out-in">
              <v-card
                :min-width="$vuetify.breakpoint.mdAndUp ? 400 : null"
                :max-width="$vuetify.breakpoint.mdAndUp ? 400 : null"
                class="text-center"
                color="transparent"
                flat
                >
                <brand></brand>
                <login-form></login-form>
              </v-card>
            </v-fade-transition>

          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  components: {
    LoginForm: () => import('./cards/LoginForm'),
  },

  data: () => ({
    app: () => import('@/config/app')
  }),
}
</script>
