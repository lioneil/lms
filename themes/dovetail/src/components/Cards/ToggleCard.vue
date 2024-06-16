<template>
  <v-card class="text-center">
    <slot name="illustration"></slot>
    <slot name="title"></slot>
    <div class="d-flex align-center justify-center">
      <v-switch inset color="primary" v-model="dataset" false-value="false" true-value="true"></v-switch>
    </div>
    <slot name="text"></slot>
    <input type="hidden" :name="name" v-model="dataset">
  </v-card>
</template>

<script>
export default {
  name: 'ToggleCard',

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
