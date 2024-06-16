<template>
  <div>
    <v-skeleton-loader class="mb-5" type="image"></v-skeleton-loader>

    <v-row>
      <v-col cols="12" md="8" class="order-2 order-md-1">
        <div class="mb-5">
          <h3 class="mb-3" v-text="trans('Description')"></h3>
          <v-skeleton-loader height="28" class="mb-3" type="image" width="80%"></v-skeleton-loader>
          <v-skeleton-loader height="20" class="mb-3" type="image" width="50%"></v-skeleton-loader>
          <v-skeleton-loader height="20" class="mb-3" type="image" width="70%"></v-skeleton-loader>
          <v-skeleton-loader height="20" class="mb-3" type="image" width="40%"></v-skeleton-loader>
        </div>
      </v-col>

      <v-col cols="12" md="4" class="order-1 order-md-2">
        <v-card>
          <v-card-text>
            <v-row align="center">
              <v-col cols="auto">
                <v-skeleton-loader type="avatar"></v-skeleton-loader>
              </v-col>
              <v-col>
                <v-skeleton-loader type="sentences"></v-skeleton-loader>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>

          <!-- Playlist -->
          <div :key="n" v-for="n in 3">
            <v-skeleton-loader color="transparent" type="list-item-avatar-two-line"></v-skeleton-loader>
          </div>
          <!-- Playlist -->
        </v-card>
      </v-col>
    </v-row>

    <v-card color="transparent" flat height="300"></v-card>
  </div>
</template>
