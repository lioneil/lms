<template>
  <v-card class="mb-3">
    <v-card-title>{{ trans('Metainfo') }}</v-card-title>
    <v-list v-if="listIsNotEmpty" dense disabled>
      <v-list-item-group color="primary">
        <v-list-item v-for="(item, i) in list" :key="i">
          <v-list-item-icon v-if="item.icon">
            <v-icon small v-text="item.icon"></v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title class="muted--text text-wrap" v-text="item.text"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
    <v-card-text v-else class="pt-0 muted--text text-center">
      <div class="mb-2" style="filter: grayscale(0.5);">
        <empty-icon width="60" height="60"></empty-icon>
      </div>
      <div v-html="trans('No information available.')"></div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: 'MetainfoCard',

  props: {
    list: {
      type: [Array, Object],
      default: () => [],
    }
  },

  computed: {
    listIsNotEmpty () {
      return this.list.length
    },
  },
}
</script>
