<template>
  <v-app v-cloak toolbar footer>
    <!-- Main Content -->

    <v-main>
      <!-- Preloader -->
      <!-- <preloader></preloader> -->
      <!-- Preloader -->

      <!-- Main -->
      <v-slide-y-transition mode="out-in">
        <router-view></router-view>
      </v-slide-y-transition>
      <!-- Main -->
    </v-main>
    <!-- Main Content -->

    <!-- <progressbar></progressbar> -->
  </v-app>
</template>

<script>
import app from '@/config/app'

export default {
  name: 'Auth',
  data: () => ({
    app
  })
}
</script>
