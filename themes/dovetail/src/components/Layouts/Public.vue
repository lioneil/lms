<template>
  <v-app v-cloak toolbar footer class="dovetail-app">
    <!-- Main Content -->

    <!-- <v-main> -->
      <!-- Main -->
      <v-slide-y-transition mode="out-in">
        <router-view></router-view>
      </v-slide-y-transition>
      <!-- Main -->
    <!-- </v-main> -->
    <!-- Main Content -->
  </v-app>
</template>
