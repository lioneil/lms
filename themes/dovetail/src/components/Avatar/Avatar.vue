<template>
  <div class="d-flex justify-center align-center">
    <div class="dt-avatar-preview">
      <v-avatar size="160">
        <img :src="dataset.avatar" alt="">
      </v-avatar>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      dataset: {},
    }
  },

  mounted () {
    this.dataset = Object.assign({}, this.avatar, this.items)
  },

  computed: {
    ...mapGetters({
      avatar: 'avatar/avatar'
    })
  }
}
</script>
