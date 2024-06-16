<template>
  <v-card class="mb-3">
    <v-card-text>
      <div class="d-flex fill-height justify-space-between align-center">
        <div>
          <h3
            class="muted--text font-weight-regular text-uppercase mb-3"
            v-text="dataset.title"
            >
          </h3>
          <div class="d-flex align-center">
            <h1 class="font-weight-regular" v-text="dataset.count"></h1>
            <div v-if="dataset.badge" class="dt-badge soft-error mx-2">
              <span class="error--text">{{ __('+') }}</span>
              <span class="error--text" v-text="dataset.deactivated"></span>
              <span class="error--text" v-text="trans('Deactivated')"></span>
            </div>
          </div>
        </div>
        <div>
          <v-icon v-text="dataset.icon" medium class="muted--text"></v-icon>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Glance',

  props: ['items'],

  data () {
    return {
      dataset: {},
    }
  },

  mounted () {
    this.dataset = Object.assign({}, this.glance, this.items)
  },

  computed: {
    ...mapGetters({
      glance: 'glance/glance'
    })
  }
}
</script>
