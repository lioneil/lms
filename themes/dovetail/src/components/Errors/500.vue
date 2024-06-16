<template>
  <v-app v-cloak toolbar footer class="dovetail-app">
    <v-slide-y-transition mode="out-in">
      <v-container fill-height>
        <v-col cols="12">
          <v-row justify="center" align="center">
            <v-col cols="12" md="8">
              <h1 class="display-4 muted--text mb-5">{{ __('500') }}</h1>
              <h2 class="mb-3">{{ __('Woopsie! That was very bad. Very bad, indeed.') }}</h2>
              <p classs="muted--text mb-5">{{ __('We are sorry but you do not have permission to access this page') }}</p>

              <v-btn large color="primary" exact to="dashboard" v-text="trans('Return to your dashboard')"></v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-container>
    </v-slide-y-transition>
  </v-app>
</template>
