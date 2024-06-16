<template>
  <v-row>
    <v-col>
      <v-row>
        <v-col cols="6" md="4" v-for="n in 6" :key="n">
          <v-skeleton-loader type="card-avatar"></v-skeleton-loader>
        </v-col>
      </v-row>
    </v-col>

    <v-col cols="auto" md="2">
      <v-row>
        <v-col cols="12" v-for="n in 3" :key="n">
          <v-skeleton-loader height="20" type="image"></v-skeleton-loader>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>
