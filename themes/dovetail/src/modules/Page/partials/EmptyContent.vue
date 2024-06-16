<template>
  <v-card flat color="transparent" class="text-center muted--text">
    <div class="icon--disabled">
      <empty-icon class="primary--text"></empty-icon>
    </div>
    <h3 class="muted--text">{{ trans('No content found') }}</h3>
    <p class="muted--text mb-8">
      {{ trans("Start adding content.") }}
    </p>
    <slot name="actions"></slot>
  </v-card>
</template>
