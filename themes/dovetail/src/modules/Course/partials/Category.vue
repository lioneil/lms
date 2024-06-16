<template>
  <div>
    <!-- <v-chip
      :color="$vuetify.theme.isDark ? 'blue text--lighten-5 darken-4' : 'blue lighten-5'"
      class="mb-1"
      small
      text-color="blue"
      v-if="dataset.category"
      v-text="dataset.category"
    ></v-chip> -->

    <span
      v-if="dataset.category"
      v-text="dataset.category"
    ></span>

    <span
      class="font-italic body-2 muted--text"
      v-else
      v-text="trans('Uncategorised')"
    ></span>
  </div>
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
