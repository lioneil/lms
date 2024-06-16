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
      open-on-click
      ripple
      item-key="id"
      item-text="title"
      :selectable="selectable"
      transition
      v-model="selected"
      >
      <template v-slot:prepend="{ item }">
        <template v-if="!selectable">
          <v-icon small right>
            mdi-book-open-outline
          </v-icon>
          <v-icon small left class="ml-n4">mdi-circle-outline</v-icon>
        </template>
      </template>
      <template v-slot:label="{ item }">
        <div class="pa-3">
          <div>
            <div class="mb-2">{{ item.title }}</div>
            <div :title="item.description" :class="description" class="text-wrap muted--text body-2">
              {{ item.description }}
            </div>
          </div>
        </div>
      </template>
    </v-treeview>
  </section>
</template>

<script>
export default {
  props: ['search', 'items', 'resource', 'value', 'selectable', 'description'],

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
  }
}
</script>
