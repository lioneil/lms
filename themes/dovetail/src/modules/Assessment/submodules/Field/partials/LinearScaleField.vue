<template>
  <v-card :color="$vuetify.theme.isDark ? 'workspace' : 'white'" flat>
    <v-card-title class="px-0" v-text="trans('Linear Scale')"></v-card-title>
    <v-slider
      :value="this.value ? this.value.length : 5"
      @end="generateValue"
      step="1"
      thumb-label
      ticks
      min="5"
      max="10"
    ></v-slider>
    <small v-text="trans('The answerer can select one item as an answer.')"></small>
    <item-repeater
      :noRemove="true"
      :readonly="true"
      :removeAddItem="true"
      :value="items"
      @input="update"
      ></item-repeater>
    <input type="hidden" name="type" value="linear scale">
    <input type="hidden" name="metadata[type]" value="LinearScaleField">
  </v-card>
</template>

<script>
export default {
  props: ['value'],

  computed: {
    items: {
      get () {
        return this.value || this.generateValue(5)
      },

      set (val) {
        this.item = val
      }
    }
  },

  data: () => ({
    length: ''
  }),

  components: {
    ItemRepeater: () => import('./ItemRepeater')
  },

  methods: {
    update (val) {
      this.$emit('input', val)
    },

    generateValue (length) {
      let items = []
      for(let i = 1; i <= length; i++)
        items.push({ text: i })
      this.update(items)
      return items
    }
  },
}
</script>
