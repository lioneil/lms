<template>
  <!-- <v-slide-y-transition mode="out-in"> -->
    <router-view></router-view>
  <!-- </v-slide-y-transition> -->
</template>

<script>
import multiguard from 'vue-router-multiguard';
import permissions from '@/routes/middleware/permissions'

export default {
  name: 'App',
}
</script>
