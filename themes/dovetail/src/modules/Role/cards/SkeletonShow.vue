<template>
  <v-row>
    <v-col cols="12" md="7">
      <v-card>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-skeleton-loader height="30" type="image"></v-skeleton-loader>
            </v-col>
            <v-col cols="12">
              <v-skeleton-loader height="30" type="image"></v-skeleton-loader>
            </v-col>
            <v-col cols="12">
              <v-skeleton-loader height="30" type="image"></v-skeleton-loader>
            </v-col>
          </v-row>
        </v-card-text>
        <div class="d-flex">
          <v-divider></v-divider>
          <v-icon small color="muted" class="mx-3 mt-n2">mdi-shield-lock</v-icon>
          <v-divider></v-divider>
        </div>
        <v-card-text>
          <h4 class="mb-5">{{ trans('Permissions') }}</h4>
          <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>
