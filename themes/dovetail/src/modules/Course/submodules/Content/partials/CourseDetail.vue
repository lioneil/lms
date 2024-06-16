<template>
  <v-row class="mb-4">
    <v-col cols="12" md="3" class="px-md-0">
      <div class="dt-avatar-preview d-block mx-auto mx-md-0">
        <v-avatar size="160">
          <img :src="dataset.image">
        </v-avatar>
      </div>
    </v-col>

    <v-col cols="12" md="9">
      <div class="text-center text-md-left">
        <v-chip v-if="dataset.category" class="mb-2" small text-color="blue" :color="$vuetify.theme.isDark ? 'blue text--lighten-5 darken-4' : 'blue lighten-5'" v-text="dataset.category"></v-chip>
      </div>
      <h2 class="mb-0 text-center text-md-left" v-text="dataset.title"></h2>
      <p class="mb-0 text-center text-md-left" v-text="dataset.subtitle"></p>
      <p class="mb-0 text-center text-md-left" v-text="dataset.code"></p>
      <p v-if="dataset.meta.lessons.count" class="mb-0 text-center text-md-left" v-text="`${dataset.meta.lessons.count} Lessons`"></p>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'CourseDetail',

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
