<template>
  <empty-state>
    <template v-slot:actions>
      <v-btn
        :to="{name: 'pages.create'}"
        class="mb-10"
        color="primary"
        exact
        large
        >
        <v-icon small left>mdi-file-outline</v-icon>
        <span v-text="trans('Add Page')"></span>
      </v-btn>
    </template>
  </empty-state>
</template>
