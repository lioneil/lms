<template>
  <div ref="player-container">
    <v-responsive :aspect-ratio="16/9">
      <iframe
        :src="dataset.content"
        allow="fullscreen"
        frameborder="0"
        height="100%"
        width="100%"
      ></iframe>
    </v-responsive>
    <portal to="header:buttons">
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-on="on" icon @click="toggleFullscreen">
            <v-icon>mdi-fullscreen</v-icon>
          </v-btn>
        </template>
        <span v-text="trans('Toggle Fullscreen')"></span>
      </v-tooltip>
    </portal>
  </div>
</template>

<script>
import Fullscreen from '@/mixins/fullscreen';

export default {
  props: ['value'],

  mixins: [Fullscreen ],

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  methods: {
    toggleFullscreen () {
      this.fullscreen(this.$refs['player-container']);
    }
  }
}
</script>
