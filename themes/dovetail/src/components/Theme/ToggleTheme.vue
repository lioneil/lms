<template>
  <v-btn icon @click="$store.dispatch('theme/toggle', {vm: vuetify, dark: !dark})">
    <v-icon>mdi-theme-light-dark</v-icon>
  </v-btn>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  computed: {
    ...mapGetters({
      dark: 'theme/dark',
    }),

    vuetify: function () {
      return this.$vuetify
    },
  },

  methods: {
    ...mapActions({
      toggleTheme: 'theme/toggle',
    }),
  },
}
</script>
