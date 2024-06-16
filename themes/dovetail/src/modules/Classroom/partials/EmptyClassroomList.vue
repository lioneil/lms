<template>
  <empty-state>
    <template v-slot:actions>
      <v-btn
        :to="{name: 'classrooms.create'}"
        class="mb-10"
        color="primary"
        exact
        large
        >
        <v-icon small left>mdi-book-open-outline</v-icon>
        <span v-text="trans('Add Classroom')"></span>
      </v-btn>
    </template>
  </empty-state>
</template>
