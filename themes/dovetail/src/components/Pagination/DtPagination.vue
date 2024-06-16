<template>
  <div>
    <v-card flat color="transparent" height="100"></v-card>
    <v-pagination
      v-model="page"
      :length="length"
      circle
    ></v-pagination>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: [String, Object],
      default: () => ({}),
    },

    perPage: {
      type: [String, Number],
      default: 15,
    },

    maxLength: {
      type: [String, Number],
      default: 7,
    }
  },

  computed: {
    page: {
      get () {
        return this.value.current_page
      },
      set (val) {
        this.$emit('change', Object.assign(this.params, { page: val }))
      }
    },

    length () {
      return this.value.last_page || this.maxLength
    },

    params () {
      return {
        per_page: this.perPage || this.value.per_page,
        page: this.value.current_page,
        sort: this.value.sortBy || undefined,
        order: this.value.sortDesc || false ? 'desc' : 'asc',
      }
    },
  },

  watch: {
    params: function (val) {
      this.$router.push({
        query: Object.assign({}, this.$route.query, val)
      }).catch(err => {})
    },
  },
}
</script>
