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
      :selectable="selectable"
      transition
      v-model="selected"
      >
      <template v-slot:prepend="{ item }">
        <template v-if="!selectable">
          <v-icon small right v-if="item.children">
            mdi-shield-lock
          </v-icon>
          <v-icon v-else small left class="ml-n4">mdi-circle-outline</v-icon>
        </template>
      </template>
      <template v-slot:label="{ item }">
        <div class="pa-3">
          <div v-if="item.children" :class="item.children ? '' : 'muted--text'">
            {{ item.name }}
          </div>
          <div v-else>
            <div class="mb-2">{{ item.code }}</div>
            <div class="text-wrap muted--text body-2">
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
