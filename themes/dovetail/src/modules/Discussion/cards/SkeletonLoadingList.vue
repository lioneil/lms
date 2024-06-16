<template>
  <v-row>
    <v-col cols="12" md="3" order="2" offset-md="1" class="d-none d-md-block">
      <v-card>
        <template v-for="n in 3">
          <v-skeleton-loader class="mb-3" type="list-item-two-line"></v-skeleton-loader>
        </template>
      </v-card>
    </v-col>

    <v-col cols="12" order="1" md="8">
      <template v-for="n in 5">
        <v-skeleton-loader class="mb-3" type="list-item-avatar-two-line"></v-skeleton-loader>
      </template>
    </v-col>
  </v-row>
</template>
