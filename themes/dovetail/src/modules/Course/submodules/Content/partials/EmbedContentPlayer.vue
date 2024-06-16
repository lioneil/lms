<template>
  <span v-html="dataset.content"></span>
</template>

<script>
export default {
  props: ['value'],

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
