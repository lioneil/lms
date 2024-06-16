<template>
  <v-card-text class="ck-content" v-html="dataset.content"></v-card-text>
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
