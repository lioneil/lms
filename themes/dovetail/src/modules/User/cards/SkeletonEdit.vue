<template>
  <div>
    <v-card class="mb-3" flat color="transparent">
      <v-card-title class="pa-0">{{ trans('Account Information') }}</v-card-title>
      <v-card-text class="pa-0">
        <v-row justify="space-between">
          <v-col cols="6" md="2">
            <v-skeleton-loader class="mb-6" height="40" type="image"></v-skeleton-loader>
          </v-col>
          <v-col cols="6" md="2">
            <v-skeleton-loader class="mb-6" height="40" type="image"></v-skeleton-loader>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="4">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
          <v-col cols="12" md="4">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
          <v-col cols="12" md="4">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="6">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
          <v-col cols="12" md="6">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
        </v-row>
        <v-row align="center">
          <v-col cols="12" md="6">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
          <v-col cols="12" md="6">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card class="mb-6">
      <v-card-title class="pb-0">{{ trans('Additional Background Details') }}</v-card-title>
      <v-card-text>
        <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>

        <v-skeleton-loader class="mb-6" height="56" type="image"></v-skeleton-loader>
      </v-card-text>
    </v-card>
  </div>
</template>
