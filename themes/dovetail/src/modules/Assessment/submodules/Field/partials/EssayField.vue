<template>
  <v-card :color="$vuetify.theme.isDark ? 'workspace' : 'white'" flat>
    <v-card-title class="px-0" v-text="trans('Essay')"></v-card-title>
    <small v-text="trans('The answerer must enter a paragraph as an answer.')"></small>
    <input type="hidden" name="type" value="essay">
    <input type="hidden" name="metadata[type]" value="EssayField">
  </v-card>
</template>
