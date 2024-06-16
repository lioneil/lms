<template>
  <v-row>
    <v-col cols="12" md="8">
      <!-- course detail -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-skeleton-loader class="dt-skeleton-avatar-upload" type="avatar"></v-skeleton-loader>
        </v-col>

        <v-col cols="12" md="8" offset-md="1">
          <skeleton class="mb-5" type="chip"></skeleton>
          <v-row no-gutters>
            <v-col cols="12"><skeleton type="text" class="mb-3"></skeleton></v-col>
            <v-col cols="10"><skeleton type="text" class="mb-3"></skeleton></v-col>
            <v-col cols="5"><skeleton type="text" class="mb-3"></skeleton></v-col>
          </v-row>
        </v-col>
      </v-row>
      <!-- course detail -->

      <!-- Contents -->
      <v-skeleton-loader class="mb-5" type="image" height="40" width="100%"></v-skeleton-loader>
      <v-row :key="n" v-for="n in 3">
        <v-col cols="auto">
          <v-skeleton-loader class="mb-5" type="avatar"></v-skeleton-loader>
        </v-col>
        <v-col>
          <v-skeleton-loader class="mb-5" type="paragraph"></v-skeleton-loader>
        </v-col>
      </v-row>
      <!-- Contents -->
    </v-col>

    <v-col cols="12" md="3" offset-md="1">
      <div class="sticky-w-space">
        <v-card class="mb-3">
          <v-card-title v-text="trans('Add Content')">
            <v-spacer></v-spacer>
            <v-icon color="primary">mdi-plus</v-icon>
          </v-card-title>
          <v-divider></v-divider>
          <div :key="n" v-for="n in 3">
            <v-skeleton-loader type="list-item-avatar-two-line"></v-skeleton-loader>
          </div>
        </v-card>
      </div>
    </v-col>
  </v-row>
</template>
