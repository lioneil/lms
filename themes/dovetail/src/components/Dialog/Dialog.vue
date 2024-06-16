<template>
  <v-dialog
    :max-width="dialog.maxWidth"
    :persistent="persistent"
    :width="width || dialog.width"
    scrollable
    v-model="show"
    >
    <v-card :dark="dialog.dark" :class="{ 'text-xs-center': dialog.alignment == 'center' }">
      <v-card-text>
        <slot name="illustration">
          <div class="text-center pa-3" :class="`${dialog.color}--text`">
            <component :width="dialog.illustrationWidth" :height="dialog.illustrationHeight" :is="dialog.illustration"></component>
          </div>
        </slot>
        <v-card-title class="px-0">
          <slot name="title">{{ dialog.title }}</slot>
        </v-card-title>
        <slot name="text"><p v-html="text"></p></slot>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>

        <v-btn
          v-if="dialog.buttons.cancel.show"
          :color="dialog.buttons.cancel.color"
          @click.native="dialog.buttons.cancel.callback"
          text
          >
          {{ trans(dialog.buttons.cancel.text) }}
        </v-btn>

        <v-btn
          :color="dialog.buttons.action.color"
          :disabled="dialog.loading"
          :loading="dialog.loading"
          @click.native="dialog.buttons.action.callback"
          text
          v-if="dialog.buttons.action.show"
          >
          {{ trans(dialog.buttons.action.text) }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import clone from 'lodash/clone'
import { mapGetters } from 'vuex'

export default {
  name: 'Dialogbox',

  props: ['width'],

  computed: {
    ...mapGetters({
      dialog: 'dialog/dialog'
    }),

    show: {
      get () {
        return this.dialog.show
      },
      set (val) {
        this.$store.dispatch('dialog/prompt', { show: val })
      },
    },

    persistent () {
      return clone(this.dialog.persistent)
    },

    text () {
      if (this.dialog.text instanceof Array) {
        return this.dialog.text.map((text) => {
          return '<p>'+this.trans(text)+'</p>'
        }).join('')
      }

      return this.dialog.text
    },
  },
}
</script>
