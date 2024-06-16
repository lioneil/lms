<template>
  <div ref="player-container">
    <v-responsive :aspect-ratio="16/9">
      <iframe
        :src="dataset.scorm"
        allow="fullscreen"
        class="dt-player-scorm"
        frameborder="0"
        height="auto"
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
import { mapActions } from 'vuex'

export default {
  props: ['value'],

  mixins: [ Fullscreen ],

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
    ...mapActions({
      setScormConfig: 'scorm/configure',
    }),

    toggleFullscreen () {
      this.fullscreen(this.$refs['player-container']);
    }
  },

  mounted () {
    this.setScormConfig({ debug: true })
    console.log(this.$store.getters['scorm/scorm'])
  }
}
</script>
