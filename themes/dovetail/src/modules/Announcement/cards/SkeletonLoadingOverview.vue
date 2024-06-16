<template>
  <section>
    <!-- Announcement detail -->
    <v-card outlined class="mb-6">
      <v-card-text>
        <v-container>
        <v-row>
          <v-col cols="12" md="auto">
            <v-skeleton-loader class="dt-skeleton-avatar-upload" type="avatar"></v-skeleton-loader>
          </v-col>
          <v-col cols="12" md="8">
            <skeleton type="chip" class="mb-3"></skeleton>
            <v-skeleton-loader type="paragraph"></v-skeleton-loader>
          </v-col>
        </v-row>
      </v-container>
      </v-card-text>
    </v-card>
    <!-- Announcement detail -->

    <!-- Content -->
    <v-container>
      <v-skeleton-loader class="mb-5" type="paragraph"></v-skeleton-loader>
    </v-container>
    <!-- Content -->
  </section>
</template>
