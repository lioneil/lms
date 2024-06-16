<template>
  <v-text-field
    :placeholder="trans('Search...')"
    background-color="bar"
    class="dt-text-field__search mb-3"
    clear-icon="mdi-close-circle-outline"
    clearable
    filled
    flat
    full-width
    hide-details
    single-line
    solo
    v-model="field"
  ></v-text-field>
</template>

<script>
export default {
  props: ['value'],

  computed: {
    field: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      }
    },
  }
}
</script>
