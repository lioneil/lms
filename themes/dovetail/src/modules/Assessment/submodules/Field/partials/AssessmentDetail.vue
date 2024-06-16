<template>
  <v-row class="mb-4">
    <v-col cols="12">
      <h2 class="mb-0 text-center text-md-left" v-text="dataset.title"></h2>
      <p class="mb-0 text-center text-md-left" v-text="dataset.subtitle"></p>
      <p class="mb-0 text-center text-md-left" v-text="dataset.description"></p>
      <!-- <p v-if="dataset.meta.lessons.count" class="mb-0 text-center text-md-left" v-text="`${dataset.meta.lessons.count} Lessons`"></p> -->
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'AssessmentDetail',

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
