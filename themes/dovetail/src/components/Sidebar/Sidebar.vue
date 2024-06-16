<template>
  <v-navigation-drawer
    :clipped="sidebar.clipped"
    :mini-variant.sync="sidebar.mini"
    app
    floating
    width="290"
    fixed
    v-model="sidebarmodel"
    class="dt-sidebar workspace"
    >
    <!-- Brand -->
    <v-list height="83" class="sidebar">
      <v-list-item class="mt-2">
        <v-list-item-avatar tile>
          <img :src="app.logo" :lazy-src="app.logo" width="48">
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title class="primary--text font-weight-bold" v-html="app.title"></v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <!-- <div class="text-center sidebar" style="height: 83px;">
      <v-row align="center" justify="center">
        <v-img
          class="my-3"
          :src="app.logo"
          :lazy-src="app.logo"
          max-width="60"
        ></v-img>
      </v-row>
    </div> -->
    <!-- Brand -->

    <!-- Menu Items -->
    <v-list nav class="workspace">
      <template v-for="(parent, i) in menus">
        <!-- Menu with children -->
        <template v-if="parent.meta.divider">
          <v-divider :key="i" class="my-2"></v-divider>
        </template>
        <template v-else-if="parent.meta.subheader">
          <v-subheader class="text--muted text-capitalize" :key="i" v-text="$t(parent.meta.title)"></v-subheader>
        </template>
        <template v-else-if="parent.children">
          <can :code="parent.meta.permission" :viewable="parent.meta['viewable:superadmins']">
            <v-list-group
              :key="i"
              no-action
              :prepend-icon="parent.meta.icon"
              :value="active(parent)"
              >
              <template v-slot:activator>
                <v-list-item-content :title="parent.meta.description">
                  <v-list-item-title v-text="$t(parent.meta.title)"></v-list-item-title>
                </v-list-item-content>
              </template>
              <!-- Submenu children -->
              <template v-for="(submenu, j) in parent.children">
                <v-divider v-if="submenu.meta.divider" :key="i"></v-divider>
                <template v-else>
                  <template v-if="submenu.children"></template>
                  <template v-else-if="submenu.meta.divider">
                    <v-divider :key="i"></v-divider>
                  </template>
                  <template v-else>
                    <can :code="submenu.meta.permission" :viewable="submenu.meta['viewable:superadmins']">
                      <v-list-item
                        :key="j"
                        :target="submenu.meta.external ? '_blank' : null"
                        :to="{ name: submenu.name }"
                        :exact="inactive(submenu)"
                        >
                        <v-list-item-icon v-if="submenu.meta.icon">
                          <v-icon small v-text="submenu.meta.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-content :title="submenu.meta.description">
                          <v-list-item-title v-text="$t(submenu.meta.title)"></v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </can>
                  </template>
                </template>
              </template>
            </v-list-group>
          </can>
        </template>
        <!-- Menu with Children -->
        <!-- Menu without Children -->
        <template v-else>
          <can :code="parent.meta.permission" :viewable="parent.meta['viewable:superadmins']">
            <v-list-item color="primary" :key="i" link exact :to="{name: parent.name}">
              <v-list-item-icon>
                <v-icon small v-text="parent.meta.icon"></v-icon>
              </v-list-item-icon>
              <v-list-item-content :title="parent.meta.description">
                <v-list-item-title v-text="$t(parent.meta.title)"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </can>
        </template>
        <!-- Menu without Children -->
      </template>
    </v-list>
    <!-- Menu Items -->

    <!-- Sidebar Footer -->
    <template v-slot:append>
      <div class="px-4 py-2 d-flex justify-space-between align-center">
        <v-spacer></v-spacer>
        <!-- <v-btn icon @click="$store.dispatch('theme/toggle', {vm: vuetify, dark: !dark})">
          <v-icon>mdi-theme-light-dark</v-icon>
        </v-btn> -->
        <toggle-theme></toggle-theme>
      </div>
    </template>
    <!-- Sidebar Footer -->
  </v-navigation-drawer>
</template>

<script>
import app from '@/config/app'
import includes from 'lodash/includes'
import menus from '@/config/sidebar'
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Sidebar',

  computed: {
    ...mapGetters({
      sidebar: 'sidebar/sidebar',
      dark: 'theme/dark',
      lang: 'app/locale',
    }),

    app: function () {
      return app
    },

    vuetify: function () {
      return this.$vuetify
    },

    menus: function () {
      return menus
    },

    sidebarmodel: {
      set (value) {
        this.toggle({ model: value })
      },
      get () {
        return this.sidebar.model
      },
    },
  },

  methods: {
    ...mapActions({
      toggle: 'sidebar/toggle',
      clip: 'sidebar/clip',
      toggleTheme: 'theme/toggle',
    }),

    inactive (path) {
      return !this.active(path)
    },

    active (path) {
      return includes(path.meta.children, this.$route.name)
    }
  },
}
</script>
