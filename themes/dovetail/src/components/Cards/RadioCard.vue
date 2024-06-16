<template>
  <v-card>
    <slot name="title"></slot>
    <div class="text-center">
      <slot name="illustration"></slot>
    </div>
    <v-card-text>
      <slot name="radio"></slot>
      <slot name="text"></slot>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: 'RadioCard',

  props: ['value', 'name'],

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
}
</script>
