<template>
  <v-progress-linear
    :indeterminate="progressbar.indeterminate"
    :value="progressbar.progress"
    class="dt-progressbar"
    color="primary"
    fixed
    top
    v-show="progressbar.loading"
    >
  </v-progress-linear>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters({
      progressbar: 'progressbar/progressbar',
    })
  }
}
</script>
