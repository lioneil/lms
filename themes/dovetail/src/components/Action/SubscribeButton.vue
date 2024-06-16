<template>
  <v-btn large color="primary" @click="subscribe" v-text="trans('Subscribe')"></v-btn>
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
    subscribe () {
      axios.post(this.url, {
        data: this.params
      })
      .then(response => {
        this.$emit('subscribed', response.data)
      })
    },
  }
}
</script>
