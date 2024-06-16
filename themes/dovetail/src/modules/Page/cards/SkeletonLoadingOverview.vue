<template>
  <section>
    <v-row>
      <v-col cols="12">
        <skeleton type="chip" class="mb-3"></skeleton>
        <v-skeleton-loader height="150" type="image" class="round"></v-skeleton-loader>
      </v-col>
      <v-col cols="3">
        <v-skeleton-loader type="text" class="mb-3"></v-skeleton-loader>
        <v-skeleton-loader type="text"></v-skeleton-loader>
      </v-col>
    </v-row>
  </section>
</template>
