<template>
  <section>
    <!-- Course detail -->
    <v-card outlined class="mb-6">
      <v-card-text>
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="8" class="order-2 order-md-1 offset-md-1">
            <v-skeleton-loader type="paragraph"></v-skeleton-loader>
            <v-col><skeleton type="button"></skeleton></v-col>
          </v-col>
          <v-col cols="12" md="3" class="order-1 order-md-2">
            <v-skeleton-loader class="dt-skeleton-avatar-upload" type="avatar"></v-skeleton-loader>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <!-- Course detail -->

    <!-- Playlist -->
    <v-row justify="center">
      <v-col cols="12" md="10">
        <v-skeleton-loader class="mb-5" type="image" height="76" width="100%"></v-skeleton-loader>
        <v-row :key="n" v-for="n in 3">
          <v-col cols="auto">
            <v-skeleton-loader class="mb-5" type="avatar"></v-skeleton-loader>
          </v-col>
          <v-col>
            <v-skeleton-loader class="mb-5" type="paragraph"></v-skeleton-loader>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <!-- Playlist -->
  </section>
</template>
