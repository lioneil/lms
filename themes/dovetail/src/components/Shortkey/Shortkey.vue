<template>
  <div v-shortkey.push="{ctrl: ['ctrl'], alt: ['alt']}" @shortkey="displayShortcuts"></div>
</template>

<script>
export default {
  name: 'Shortkey',

  methods: {
    displayShortcuts (event) {
      switch (event.srcKey) {
        case 'ctrl':
          this.$store.dispatch('shortkey/press', {ctrlIsPressed: !this.$store.getters['shortkey/ctrlIsPressed']})
          break
        case 'alt':
          this.$store.dispatch('shortkey/press', {altIsPressed: !this.$store.getters['shortkey/altIsPressed']})
          break
      }
    }
  },

}
</script>
