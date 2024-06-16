<template>
  <v-select
    :items="resource.data"
    :label="trans('Site Visit Date')"
    @change="changeMonth"
    append-icon="mdi-chevron-down"
    prepend-inner-icon="mdi-calendar"
    background-color="selects"
    class="dt-text-field"
    hide-details
    menu-props="offsetY"
    name="roles"
    outlined
    ref="roles"
    v-model="resource.month"
    >
    <template v-slot:item="{ item }">
      <span v-html="item.text"></span>
    </template>
  </v-select>
</template>


<script>
export default {
  data: (vm) => ({
    resource: {
      data: [],
      month: vm.$route.query.month,
    },
  }),

  methods: {
    getResource () {
      axios.get('/api/v1/reports/misc/months/all')
        .then(response => {
          this.resource.data = response.data
        })
    },

    changeMonth () {
      this.$router.push({ query: { month: this.resource.month }}).catch(err => {})
      this.$router.go()
    }
  },

  mounted () {
    this.getResource()
  },
}
</script>
