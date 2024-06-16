<template>
  <v-btn large color="primary" @click="unsubscribe" v-text="trans('Unsubscribe')"></v-btn>
</template>

<script>
export default {
  props: {
    url: {
      type: String
    },
    params: {
      type: Object
    }
  },

  methods: {
    unsubscribe () {
      axios.post(this.url, {
        data: this.params
      })
      .then(response => {
        this.$emit('unsubscribed', response.data)
      })
    },
  }
}
</script>
