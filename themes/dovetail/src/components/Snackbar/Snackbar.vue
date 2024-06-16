<template>
  <component :is="slideTransition" mode="out-in">
    <v-snackbar
      v-model="model"
      :bottom="snackbar.y === 'bottom'"
      :left="snackbar.x === 'left'"
      :multi-line="snackbar.mode === 'multi-line'"
      :right="snackbar.x === 'right'"
      :timeout="snackbar.timeout"
      :top="snackbar.y === 'top'"
      :vertical="snackbar.mode === 'vertical'"
      >

      <v-icon v-if="snackbar.icon" small>{{ snackbar.icon }}</v-icon>
      {{ snackbar.text }}

      <template v-slot:action>
        <v-btn
          v-if="snackbar.button.show"
          @click="snackbarCallback()"
          small
          text
          >
          <v-icon v-if="snackbar.button.icon" small>{{ snackbar.button.icon }}</v-icon>
          <template v-else>{{ snackbar.button.text }}</template>
        </v-btn>
      </template>
    </v-snackbar>
  </component>
</template>

<script>
import { mapGetters } from 'vuex'
import { VSlideYTransition, VSlideYReverseTransition } from 'vuetify/lib'

export default {
  name: 'Snackbar',

  components: {
    slideY: VSlideYTransition,
    slideYReverse: VSlideYReverseTransition
  },

  computed: {
    ...mapGetters({
      snackbar: 'snackbar/snackbar'
    }),

    slideTransition: function () {
      return this.snackbar.y === 'bottom' ? 'slide-y-reverse' : 'slide-y'
    },

    model: {
      get () {
        return this.snackbar.show
      },
      set (show) {
        this.$store.dispatch('snackbar/toggle', {show})
        return show
      }
    }
  },

  methods: {
    snackbarCallback: function () {
      this.snackbar.button.callback()
    }
  }
}
</script>
