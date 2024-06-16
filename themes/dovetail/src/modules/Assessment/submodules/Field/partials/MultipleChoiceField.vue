<template>
  <v-card :color="$vuetify.theme.isDark ? 'workspace' : 'white'" flat>
    <v-card-title class="px-0" v-text="trans('Multiple Choice')"></v-card-title>
    <small v-text="trans('The answerer can select one item as an answer.')"></small>
    <item-repeater :value="value" @input="update"></item-repeater>
    <input type="hidden" name="type" value="multiple choice">
    <input type="hidden" name="metadata[type]" value="MultipleChoiceField">
  </v-card>
</template>

<script>
export default {
  props: ['value'],

  components: {
    ItemRepeater: () => import('./ItemRepeater')
  },

  methods: {
    update (val) {
      this.$emit('input', val)
    }
  }
}
</script>
