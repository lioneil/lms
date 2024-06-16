<template>
  <admin>
    <metatag :title="trans('Widgets')"></metatag>

    <page-header>
      <template v-slot:title>
        <span v-text="trans('Widgets')"></span>
      </template>
    </page-header>

    <v-row>
      <v-col cols="12" md="4">
        <v-card>
          <v-card-title v-text="trans('Widgets')"></v-card-title>
          <v-card-text>
            <p class="text-body-2 link--text">Widgets are dynamic cards conveying easy to digest data.</p>
            <p class="text-body-2 link--text">Refreshing will install widgets specified by the modules installed.</p>
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary"
            :disabled="resources.loading.refresh"
            :loading="resources.loading.refresh"
            @click="refreshWidgetsList"
            >
              <span v-text="trans('Refresh')"></span>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title v-text="trans('Available Widgets')"></v-card-title>

          <v-list two-line>
            <v-list-item
              :key="item.id"
              :to="{ name: 'widgets.edit', params: { id: item.id }, query: { from: $route.fullPath } }"
              exact
              v-for="item in resources.data.data"
              >
              <v-list-item-avatar>
                <v-icon class="blue lighten-5 primary--text" small v-html="item.file"></v-icon>
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title v-text="trans(item.name)"></v-list-item-title>
                <v-list-item-subtitle class="link--text" v-text="trans(item.description)"></v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                  <span v-text="`0`"></span>
              </v-list-item-action>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </admin>
</template>

<script>
import $api from './routes/api'
import { mapActions } from 'vuex'

export default {
  data: () => ({
    api: $api,
    resources: {
      loading: {
        reset: false,
        refresh: false,
      },
      data: []
    },
  }),

  methods: {
    ...mapActions({
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
    }),

    getResource () {
        console.log($api.list())
      axios.get(
        $api.list()
      ).then(response => {
        this.resources.data = Object.assign([], this.resources.data, response.data)
      })
    },

    refreshWidgetsList () {
      this.resources.loading.refresh = true
      axios.post(
        this.api.refresh()
      ).then(response => {
        this.showSnackbar({
          text: trans('Widgets successfully refreshed')
        })
      }).finally(() => {
        this.resources.loading.refresh = false
      })
    },
  },

  mounted () {
    this.getResource()
  }
}
</script>
