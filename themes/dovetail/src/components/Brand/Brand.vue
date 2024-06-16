<template>
  <section class="text-center mb-3">
    <v-row align="center" justify="center">
      <v-img
        class="mb-3"
        :src="app.logo"
        :lazy-src="app.logo"
        :max-width="brand"
      ></v-img>
    </v-row>
    <h1 class="my-6" v-text="app.title"></h1>
    <!-- <p class="muted--text" v-text="app.tagline"></p> -->
    <p class="muted--text mb-10">{{ trans('Explore our accessible and engaging innovations for learning and living.') }}</p>

  </section>
</template>

<script>
import app from '@/config/app'

export default {
  data: () => ({
    app,
    brand: 80,
  }),
}
</script>

