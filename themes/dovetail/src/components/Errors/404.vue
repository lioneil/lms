<template>
  <v-app v-cloak toolbar footer class="dovetail-app">
    <v-slide-y-transition mode="out-in">
      <v-container fill-height>
        <v-col cols="12">
          <v-row justify="center" align="center">
            <v-col cols="12" md="6" offset-md="1" class="d-none d-md-flex order-md-2">
              <open-map-icon class="primary--text"></open-map-icon>
            </v-col>

            <v-col cols="12" md="5" class="order-md-1">
              <div class="text-center">
                <h3 class="text-uppercase muted--text mb-5">
                  <strong>{{ __("404 Error") }}</strong>
                </h3>
                <h2 class="mb-2">{{ __("There's no page here 😭") }}</h2>
                <p class="muted--text mb-5">
                  {{ __('Looks like you ended up here by accident?') }}
                </p>

                <v-btn class="mb-5" large color="primary" exact :to="{name: 'dashboard'}" v-text="trans('Return to your dashboard')"></v-btn>

                <!-- <superadmin-only></superadmin-only> -->
              </div>
            </v-col>
          </v-row>
        </v-col>
      </v-container>
    </v-slide-y-transition>
  </v-app>
</template>
