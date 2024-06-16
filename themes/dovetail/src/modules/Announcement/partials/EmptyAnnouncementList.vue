<template>
  <empty-state>
    <template v-slot:actions>
      <v-btn
        :to="{name: 'announcements.create'}"
        class="mb-10"
        color="primary"
        exact
        large
        >
        <v-icon small left>mdi-bullhorn-outline</v-icon>
        <span v-text="trans('Add Announcement')"></span>
      </v-btn>
    </template>
  </empty-state>
</template>
