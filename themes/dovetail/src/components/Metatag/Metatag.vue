<template>
  <keep-alive></keep-alive>
</template>

<script>
export default {
  name: 'Metatag',

  props: ['title', 'description'],

  watch: {
    title: function (val) {
      this.$store.dispatch('metatag/set', { title: val })
      document.title = `${val} | ${this.$store.getters['app/title']}`
    },

    description: function (val) {
      this.$store.dispatch('metatag/set', { description: val })
      document
            .querySelector('head meta[name="description"]')
            .setAttribute('content', val)
    },
  },
}
</script>
