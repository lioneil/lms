<template>
  <v-app-bar
    app
    :hide-on-scroll="$vuetify.breakpoint.mdAndUp"
    flat
    :clipped-left="sidebar.clipped"
    v-if="appbar.model"
    :height="$vuetify.breakpoint.mdAndUp ? 83 : null"
    >
    <v-badge
      bordered
      bottom
      class="dt-badge"
      color="dark"
      content="k"
      offset-x="20"
      offset-y="20"
      tile
      transition="fade-transition"
      v-model="$store.getters['shortkey/ctrlIsPressed']"
      >
      <v-app-bar-nav-icon color="muted" @click="toggle({model: !sidebar.model})" v-shortkey.once="['ctrl', 'k']" @shortkey="toggle({model: !sidebar.model})"></v-app-bar-nav-icon>
    </v-badge>

    <slot>
      <can code="searches.show">
        <search-form></search-form>
      </can>

      <v-spacer></v-spacer>

      <user-is-logged-in>
        <v-menu
          class="justify-end d-flex"
          min-width="200px"
          transition="slide-y-transition"
          offset-y
          nudge-bottom
          nudge-bottom
          >
          <template v-slot:activator="{ on: menu }">
            <!-- <v-tooltip bottom>
              <template v-slot:activator="{ on: tooltip }">
                <div v-on="{ ...tooltip, ...menu }" role="button"> -->
                <div v-on="{ ...menu }" role="button">
                  <div class="d-flex justify-space-between align-center">
                    <v-avatar size="38"><img :src="user.avatar" width="40px"></v-avatar>
                    <!-- <div class="d-none d-md-block">
                      <p class="body-2 mb-0 text--truncate" v-text="user.displayname"></p>
                      <div v-text="user.role" class="muted--text dt-overline"></div>
                    </div> -->
                  </div>
                </div>
              <!-- </template>
              <span v-text="user.displayname"></span>
            </v-tooltip> -->
          </template>

          <v-list max-width="250">
           <!--  <v-list-item>
              <v-list-item-action>
                <v-icon small class="text--muted">mdi-account-outline</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title v-text="trans('My Profile')"></v-list-item-title>
              </v-list-item-content>
            </v-list-item> -->

            <v-list-item>
              <v-list-item-action>
                <v-icon small>mdi-account-outline</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title v-text="user.displayname"></v-list-item-title>
                <v-list-item-subtitle class="dt-overline" v-text="user.role"></v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-divider></v-divider>

            <v-list-item :to="{ name: 'settings.general' }" exact>
              <v-list-item-action>
                <v-icon small>mdi-tune</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title v-text="trans('Settings')"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>


            <v-list-item exact :to="{name: 'logout'}">
              <v-list-item-action>
                <v-icon small>mdi-power</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title v-text="trans('Logout')"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </user-is-logged-in>
    </slot>
  </v-app-bar>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Appbar',

  data () {
    return {
    }
  },

  computed: {
    ...mapGetters({
      appbar: 'appbar/appbar',
      sidebar: 'sidebar/sidebar',
      user: 'auth/user',
    }),
  },

  methods: {
    ...mapActions({
      toggle: 'sidebar/toggle',
      logout: 'auth/logout',
    }),
  },
}
</script>
