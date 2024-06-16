<template>
  <v-card class="mb-3">
    <v-card-title>{{ trans('Metainfo') }}</v-card-title>
    <v-list dense disabled>
      <v-list-item-group color="primary">
        <v-list-item>
          <v-list-item-icon>
            <v-icon small v-text="('mdi-calendar')"></v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <v-skeleton-loader type="text"></v-skeleton-loader>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-item>
          <v-list-item-icon>
            <v-icon small v-text="('mdi-calendar-edit')"></v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              <v-skeleton-loader type="text"></v-skeleton-loader>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </v-card>
</template>
