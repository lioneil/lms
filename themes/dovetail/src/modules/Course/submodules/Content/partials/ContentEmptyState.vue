<template>
  <v-row justify="center" align="center">
    <v-card flat color="transparent" class="text-center muted--text">
      <div class="icon--disabled mb-5">
        <data-collecting-icon height="200" width="200" class="primary--text"></data-collecting-icon>
      </div>
      <h3 class="muted--text" v-text="trans('Add content to your course')"></h3>

      <slot name="text"></slot>
      <slot name="actions"></slot>
    </v-card>
  </v-row>
</template>
