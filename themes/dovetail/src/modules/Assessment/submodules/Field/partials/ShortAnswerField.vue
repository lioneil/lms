<template>
  <v-card :color="$vuetify.theme.isDark ? 'workspace' : 'white'" flat>
    <v-card-title class="px-0" v-text="trans('Short Answer')"></v-card-title>
    <small v-text="trans('The answerer must enter a few words as an answer.')"></small>
    <input type="hidden" name="type" value="short answer">
    <input type="hidden" name="metadata[type]" value="ShortAnswerField">
  </v-card>
</template>
