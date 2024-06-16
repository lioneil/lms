<template>
  <v-scale-transition mode="in-out">
    <v-btn
      :large="$vuetify.breakpoint.smAndUp"
      :small="$vuetify.breakpoint.xsOnly"
      @click="toTop"
      bottom
      class="mb-7"
      color="selects"
      dark
      fab
      fixed
      right
      style="z-index:1000;"
      v-scroll="onScroll"
      v-show="fab"
      >
      <v-icon color="muted">mdi-arrow-up-thick</v-icon>
    </v-btn>
  </v-scale-transition>
</template>

<script>
export default {
  data: () => ({
    fab: false
  }),

  methods: {
    onScroll (e) {
      if (typeof window === 'undefined') return
      const top = window.pageYOffset ||   e.target.scrollTop || 0
      this.fab = top > 20
    },
    toTop () {
      this.$vuetify.goTo(0)
    }
  }
}
</script>
