<template>
  <v-row justify="center" align="center">
    <v-card flat color="transparent" class="text-center muted--text">
      <div class="icon--disabled">
        <empty-icon class="primary--text"></empty-icon>
      </div>
      <h3 class="muted--text">{{ trans('No resource found') }}</h3>
      <p class="muted--text mb-8">
        {{ trans("This page returned empty results.") }}
      </p>
      <slot name="actions"></slot>
    </v-card>
  </v-row>
</template>
