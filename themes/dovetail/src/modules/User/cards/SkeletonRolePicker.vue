<template>
  <v-card>
    <v-card-title>{{ trans('Roles') }}</v-card-title>
    <slot name="illustration">
      <div class="secondary--text text-center">
        <shield-with-check-mark-icon></shield-with-check-mark-icon>
      </div>
    </slot>
    <v-card-text>
      <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
    </v-card-text>
  </v-card>
</template>
