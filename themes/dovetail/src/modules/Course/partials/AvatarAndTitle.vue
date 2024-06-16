<template>
  <v-tooltip bottom>
    <template v-slot:activator="{ on }">
      <router-link
        :to="{ name: 'courses.edit', params: {id: dataset.id}, query: {from: $route.fullPath} }"
        class="t-d-none"
        exact
        tag="a"
        >
        <div class="d-flex align-center">
          <v-avatar class="mr-6" size="32"><v-img :src="dataset.image"></v-img></v-avatar>
          <span v-on="on" class="text-no-wrap t-d-hover-lined" v-text="trans(dataset.title)"></span>
        </div>
      </router-link>
    </template>
    <span v-text="trans('Edit this course')"></span>
  </v-tooltip>
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
