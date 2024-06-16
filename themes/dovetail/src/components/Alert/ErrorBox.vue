<template>
  <v-alert
    :border="errorbox.border"
    :color="errorbox.color || errorbox.type"
    :dense="errorbox.dense"
    :dismissible="errorbox.dismissible"
    :icon="errorbox.icon"
    :outlined="errorbox.outlined"
    :prominent="errorbox.prominent"
    :type="errorbox.type"
    text
    transition="scale-transition"
    v-model="show"
    >
    <v-row align="center">
      <v-col class="grow">
        <div v-if="errorbox.text" class="font-weight-bold text--error mb-4" v-text="errorbox.text"></div>
        <ul v-if="errors.length">
          <li v-for="(error, e) in errors" :key="e" v-html="error"></li>
        </ul>
      </v-col>
    </v-row>
  </v-alert>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'ErrorBox',

  computed: {
    ...mapGetters({
      errorbox: 'errorbox/errorbox',
    }),

    errors () {
      return this.errorbox.errors
    },

    show: {
      get () {
        return this.errorbox.show
      },
      set (val) {
        this.$store.dispatch('errorbox/set', { show: val })
      },
    },
  }
}
</script>
