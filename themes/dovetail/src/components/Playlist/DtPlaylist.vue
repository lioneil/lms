<template>
  <v-list class="dt-list" expand>
    <template v-for="(item, i) in items">

      <template v-if="item.has_child || item.is_section">
        <v-list-group
          :color="color"
          :key="i"
          :value="active(item)"
          >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title v-text="item.title" class="text--text font-weight-bold"></v-list-item-title>
            </v-list-item-content>
          </template>
          <!-- Submenu children -->
          <template v-if="item.has_child">
            <template v-for="(subitem, k) in item.children">
              <slot name="subitem:item" v-bind="{ subitem, item }">
                <v-list-item :disabled="subitem.progress.locked" :to="url(subitem.to)" :key="k">
                  <v-list-item-avatar color="section" v-if="subitem.icon">
                    <v-icon v-if="subitem.progress.locked" small color="grey lighten-1" v-text="'mdi-lock-outline'"></v-icon>
                    <v-icon v-else small v-text="subitem.icon"></v-icon>
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title :class="submenuTitle" v-text="subitem.title"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </slot>
            </template>
          </template>

          <template v-else>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title class="muted--text font-italic" v-text="trans('Content coming soon.')"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template>
          <!-- Submenu children -->
        </v-list-group>
      </template>

      <template v-else>
        <slot name="item" v-bind="{ item }">
          <v-list-item :color="color" :to="url(item.to)">
            <v-list-item-avatar color="section" class="text-center">
              <v-icon small v-text="item.icon"></v-icon>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title class="text-wrap mb-1 text-capitalize" v-text="item.title"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </slot>
      </template>
    </template>
  </v-list>
</template>

<script>
import has from 'lodash/has'
import includes from 'lodash/includes'
import map from 'lodash/map'

export default {
  props: {
    color: {
      type: String,
      default: 'primary',
    },

    items: {
      type: Array,
      default: () => ([]),
    },

    submenuTitle: {
      type: String,
      default: 'text-wrap'
    },

    to: {
      type: Object,
      default: () => ({}),
    },
  },

  data: () => ({
    selected: [],
  }),

  methods: {
    active (item, key = 'slug') {
      let slug = this.$route.params.contentslug

      if (has(item, 'has_child') && item.has_child) {
        let children = map(item.children, (v) => (v[key]))

        return includes(children, slug)
      }

      return item[key] == slug
    },

    url (to) {
      this.scrollToTop()

      return Object.assign({}, to, {
        query: { from: this.$route.path }
      })
    },

    scrollToTop (top = 0) {
      window.scrollTo({ top: top, behavior: 'smooth' })
    }
  },

  mounted () {
    this.scrollToTop()
  },
}
</script>
