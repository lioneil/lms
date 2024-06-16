<template>
  <v-app-bar
    class="px-0 playerbar"
    app
    :hide-on-scroll="$vuetify.breakpoint.mdAndUp"
    flat
    v-if="playerbar.model"
    :height="$vuetify.breakpoint.mdAndUp ? 83 : null"
    >
    <v-container class="container--player">
      <slot>
        <div class="d-flex justify-space-between align-center">
          <!-- App logo and title -->
          <div class="d-flex align-center">
            <router-link tag="a" class="t-d-none" exact :to="{ name: 'dashboard' }">
              <v-img class="mr-3" :src="app.logo" :lazy-src="app.logo" width="40"></v-img>
            </router-link>
            <span class="primary--text" v-html="app.title"></span>
          </div>
          <!-- App logo and title -->

          <!-- Menus -->

          <!-- Menus -->

          <!-- User Account -->
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
                        <v-avatar size="38" class="mr-3"><img :src="user.avatar" width="40px"></v-avatar>
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

                <v-list-item :to="{ name: 'settings' }" exact>
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
          <!-- User Account -->
        </div>
      </slot>
    </v-container>
  </v-app-bar>
</template>

<script>
import app from '@/config/app'
import { mapGetters, mapActions } from 'vuex'

export default {
  data () {
    return {
    }
  },

  computed: {
    ...mapGetters({
      playerbar: 'playerbar/playerbar',
      user: 'auth/user',
    }),

    app () {
      return app
    },
  },

  methods: {
    ...mapActions({
      toggle: 'sidebar/toggle',
      logout: 'auth/logout',
    }),
  },
}
</script>
