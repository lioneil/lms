<template>
  <section>
    <v-treeview
      :filter="filter"
      :items="items"
      :search="search"
      color="primary"
      expand-icon="mdi-chevron-down"
      hoverable
      open-all
      :active.sync="active"
      open-on-click
      :activatable="activatable"
      :selectable="selectable"
      transition
      transition
      v-model="selected"
      return-object
      >
      <template v-slot:label="{ item }">
        <div class="py-3 px-4">
          <v-avatar class="mr-6" size="32" color="workspace">
            <v-img :src="item.avatar"></v-img>
          </v-avatar>
          <span v-text="item.displayname"></span>
        </div>
      </template>
    </v-treeview>

    <!-- pagination -->
    <!-- <div class="text-center">
      <v-pagination
        v-model="pagination"
        :length="pageLength"
        @input="goToPage"
        circle
      ></v-pagination>
    </div> -->
    <!-- pagination -->
  </section>
</template>

<script>
// import $api from '@/modules/Team/routes/api'

export default {
  props: [
    'search',
    'resource',
    'value',
    'selectable',
    'pageLength',
    'pageUrl',
    'items',
    'activatable',
  ],

  computed: {
    selected: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      }
    },

    filter () {
      return undefined
    },
  },

  data: () => ({
    pagination: 1,
    active: [],
  }),

  watch: {
    active: function (val) {
      this.$emit('active', val)
    }
  },
}
</script>
