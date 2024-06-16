<template>
  <admin>
    <metatag :title="trans('All Permissions')"></metatag>

    <page-header></page-header>

    <v-row>
      <v-col cols="12" md="5">
        <!-- Treeview -->
        <v-card flat color="transparent" class="mb-4">
          <v-card-text class="pa-0">
            <v-text-field
              :placeholder="trans('Search...')"
              autofocus
              background-color="bar"
              class="dt-text-field__search"
              clear-icon="mdi-close-circle-outline"
              clearable
              filled
              flat
              full-width
              hide-details
              prepend-inner-icon="mdi-magnify"
              single-line
              solo
              v-model="search"
            ></v-text-field>
            <v-treeview
              :filter="filter"
              :items="resources.data"
              :search="search"
              color="primary"
              hoverable
              expand-icon="mdi-chevron-down"
              ripple
              transition
              >
              <template v-slot:prepend="{ item }">
                <v-icon small right v-if="item.children">
                  mdi-shield-lock
                </v-icon>
                <v-icon v-else small left class="ml-n4">mdi-circle-outline</v-icon>
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
          </v-card-text>
        </v-card>
        <!-- Treeview -->
      </v-col>

      <v-col cols="12" class="offset-md-1" md="6">
        <v-card flat color="transparent">
          <h3 class="mb-4">{{ trans('Refresh List') }}</h3>
          <p class="text--text">{{ trans('Refreshing will add and/or update all new permissions specified by the modules you\'ve installed. Outdated permissions or permissions from uninstalled modules will be removed.') }}</p>

          <v-btn
            :block="$vuetify.breakpoint.smAndDown"
            :disabled="resources.loading.refresh"
            :loading="resources.loading.refresh"
            @click="refreshPermissionsList()"
            color="primary"
            large
            >
            <v-icon small dark left>mdi-refresh</v-icon>
            {{ trans('Refresh Permission') }}
            <template v-slot:loader>
              <span>
                <v-slide-x-transition>
                  <v-icon small left dark class="mdi-spin">mdi-loading</v-icon>
                </v-slide-x-transition>
                {{ trans('Refreshing...') }}
              </span>
            </template>
          </v-btn>
        </v-card>

        <v-divider class="my-8"></v-divider>

        <v-card flat color="transparent">
          <h3 class="mb-4">{{ trans('Reset List') }}</h3>
          <p class="text--text">{{ trans('Performing this action will completely remove all permissions data from the database before reinstalling them. Users might lose their previous privileges after this action.') }}</p>

          <v-alert
            elevation="1"
            border="top"
            type="warning"
            color="secondary"
            prominent
            class="mb-6"
            text
          >
            {{ trans('You might need to setup the user roles manually again. If you do not want to upset the order of the Cosmos, then for the love of Talos, do not proceed!') }}
          </v-alert>

          <v-btn
            :block="$vuetify.breakpoint.smAndDown"
            :disabled="resources.loading.reset"
            :loading="resources.loading.reset"
            @click="askUserToResetPermission()"
            color="error"
            large
            >
            <v-icon small dark left>mdi-lock-reset</v-icon>
            {{ trans('Reset Permission') }}
            <template v-slot:loader>
              {{ trans('Resetting...') }}
            </template>
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </admin>
</template>

<script>
import $api from './routes/api'
import NotifyIcon from '@/components/Icons/NotifyIcon.vue'
import { mapActions } from 'vuex'

export default {
  data: () => ({
    api: $api,

    resources: {
      loading: {
        reset: false,
        refresh: false,
      },
      data: [],
    },
    search: null,
  }),

  mounted () {
    this.displayPermissionsList()
  },

  methods: {
    ...mapActions({
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
    }),

    displayPermissionsList () {
      axios.get(this.api.list())
      .then(response => {
        this.resources.data = Object.assign([], this.resources.data, response.data)
      })
    },

    refreshPermissionsList () {
      this.resources.loading.refresh = true
      axios.post(
        this.api.refresh()
      ).then(response => {
        this.showSnackbar({
          text: trans('Permissions successfully refreshed')
        })
      }).finally(() => {
        this.resources.loading.refresh = false
      })
    },

    askUserToResetPermission () {
      this.showDialog({
        color: 'error',
        illustration: NotifyIcon,
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '600',
        title: 'WARNING! Read before proceeding.',
        text: [
          'Resetting the permissions table will break your existing users\' established roles. Though the application will try to rebuild the permissions table, there is no guarantee all items will be restored.',
          "In fact, any manually added permission will not be recovered. You might need to setup the user roles manually again. Click outside the dialogbox if you don't want to proceed. Are you sure yout want to reset permissions?"
        ],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: 'Reset Permissions',
            color: 'error',
            callback: (dialog) => {
              this.loadDialog(true)
              this.resetPermissionsList()
            }
          }
        }
      })
    },

    resetPermissionsList () {
      this.resources.loading.reset = true
      axios.post(
        this.api.reset()
      ).then(response => {
        this.showSnackbar({
          text: trans('Permissions successfully reset')
        })
        this.loadDialog(false)
        this.hideDialog()
      }).finally(() => {
        this.resources.loading.reset = false
      })
    }
  },

  computed: {
    filter () {
      return undefined
      // return (item, search, textKey) => item[textKey].indexOf(search) > -1
    },
  },
}
</script>
