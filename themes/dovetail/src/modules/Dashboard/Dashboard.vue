<template>
  <admin>
    <page-header>
      <template v-slot:title>
        {{ trans(`Hi ${auth.firstname}`) }}!
      </template>
      <template v-slot:utilities>
        <div class="link--text">{{ trans(`You are logged in as ${auth.role} account`) }}</div>
      </template>
    </page-header>

    <!-- <template v-if="resources.loading">
      <v-row>
        <v-col cols="12" sm="6">
          <v-skeleton-loader type="image" height="200"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" sm="6">
          <v-skeleton-loader type="image" height="200"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" sm="6">
          <v-skeleton-loader type="image" height="200"></v-skeleton-loader>
        </v-col>
        <v-col cols="12" sm="6">
          <v-skeleton-loader type="image" height="200"></v-skeleton-loader>
        </v-col>
      </v-row>
    </template> -->

    <!-- <template v-else> -->
      <!-- <div v-show="resourcesIsNotEmpty">
        <v-row>
          <v-col cols="12" sm="6" v-for="(resource, i) in resources.data" :key="i">
            <v-card
              :hover="$vuetify.breakpoint.smAndUp"
              :to="$vuetify.breakpoint.smAndUp ? { name: 'companies.find' } : null"
              class="text-center"
              exact
              height="100%"
              v-ripple="$vuetify.breakpoint.smAndUp ? { class: 'primary--text' } : null"
              >
              <v-card-text>
                <div class="mb-4"><img height="80" :src="resource.icon" alt=""></div>
                <h4 class="mb-1 text-uppercase" v-text="resource.name"></h4>
                <p class="text-uppercase muted--text" v-text="('Performance Indexes')"></p>
              </v-card-text>
              <v-card-actions v-if="$vuetify.breakpoint.xsOnly">
                <v-spacer></v-spacer>
                <v-btn block large text color="primary" :to="{ name: 'companies.find' }" exact>{{ __('Start Survey') }}</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </div> -->

      <!-- Empty state -->
      <!-- <div v-if="resourcesIsEmpty"> -->
        <empty-state></empty-state>
      <!-- </div> -->
      <!-- Empty state -->
    <!-- </template> -->
  </admin>
</template>

<script>
import $auth from '@/core/Auth/auth'
import isEmpty from 'lodash/isEmpty'

export default {
  computed: {
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },
  },

  data: () => ({
    auth: $auth.getUser(),
    resources: {
      data: [],
      loading: false,
    }
  }),
}
</script>
