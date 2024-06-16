<template>
  <div v-sortable="{ forceFallback: true, onUpdate: handleEndEvent, handle: '.handle' }">
    <data-sorter-item :key="`${i}-${item.title}`" v-for="(item, i) in dataset">
      <slot name="item" v-bind="{ item }"></slot>
    </data-sorter-item>
  </div>
</template>


<script>
export default {
  props: ['items', 'value'],

  computed: {
    dataset: {
      get () {
        return this.items
      },
      set (val) {
        this.items = val
      },
    }
  },

  methods: {
    handleEndEvent (e) {
      let item = this.dataset.splice(e.oldIndex, 1)[0]
      this.dataset.splice(e.newIndex, 0, item)
      this.$emit('sorted', this.dataset)
    }
  },
}
</script>
