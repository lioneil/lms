<template>
  <web-container>
    <metatag :title="trans('All Discussions')"></metatag>

    <page-header>
      <template v-slot:utilities>
        <can code="threads.trashed">
          <router-link
            tag="a"
            class="dt-link t-d-none mr-4"
            exact
            :to="{name: 'threads.trashed', query: {from: $route.fullPath}}"
            >
            <v-icon small left>mdi-delete-outline</v-icon>
            <span v-text="trans('Trashed Discussions')"></span>
          </router-link>
        </can>
      </template>

      <template v-slot:action>
        <can code="threads.create">
          <v-btn :block="isMobile" large color="primary" exact :to="{ name: 'threads.create' }">
          <v-icon small left>mdi-chat-plus-outline</v-icon>
          <span v-text="trans('Add Discussion')"></span>
          </v-btn>
        </can>
      </template>
    </page-header>

    <!-- Items -->
    <template v-if="resources.loading">
      <skeleton-loading-list></skeleton-loading-list>
    </template>

    <template v-else>
      <v-row>
        <v-col cols="12" md="3" class="d-block d-md-none">
          <v-autocomplete
            :items="categories"
            :label="trans('Select a category')"
            append-icon="mdi-chevron-down"
            background-color="selects"
            class="dt-text-field"
            item-text="title"
            outlined
            >
            <template v-slot:no-data>
              <v-card-text v-text="trans('No category found')"></v-card-text>
            </template>
          </v-autocomplete>
        </v-col>
        <v-col cols="12" md="3" order="2" offset-md="1" class="d-none d-md-block">
          <v-card>
            <v-list dense>
              <v-list-item-group color="primary">
                <v-list-item
                  v-for="(item, i) in categories"
                  :key="i"
                  >
                  <v-list-item-icon>
                    <v-icon v-text="item.icon"></v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.title"></v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card>
        </v-col>

        <v-col cols="12" order="1" md="8">
          <template v-if="resourcesIsNotEmpty">
            <v-row :key="i" v-for="(item, i) in resources.data" class="mb-3">
              <v-col :cols="isDesktop ? 'auto' : 12">
                <v-avatar size="48" color="section">
                  <v-img :src="item.user.avatar"></v-img>
                </v-avatar>
              </v-col>
              <v-col>
                <v-hover v-slot:default="{ hover }">
                  <div class="mb-3">
                    <router-link
                      :class="{ 'text-decoration-underline': hover }"
                      :title="item.title"
                      :to="{ name: 'threads.show', params: {id: item.id} }" v-text="item.title"
                      class="text--text font-weight-bold text-decoration-none"
                      exact
                      tag="a"
                    ></router-link>
                  </div>
                </v-hover>
                <div class="d-flex justify-space-between">
                  <div class="body-2">
                    <span class="primary--text" v-text="item.user.displayname"></span>
                    <span class="muted--text" v-text="`posted ${item.created}`"></span>
                  </div>
                </div>
              </v-col>
              <v-col cols="auto">
                <div class="mb-3 text-right">
                  <v-icon small color="muted">mdi-chat-outline</v-icon>
                  <small class="muted--text" v-text="trans('1')"></small>
                </div>
                <can code="threads.owned">
                  <router-link
                    tag="a"
                    exact
                    class="dt-link t-d-none mr-4"
                    :to="{ name: 'threads.edit', params: { id: item.id } }"
                    v-text="trans('Edit')"
                  ></router-link>
                </can>
                <can code="threads.owned">
                  <a
                    @click="askUserToPermanentlyDeleteResource(item)"
                    class="dt-link t-d-none"
                    v-text="trans('Delete')"
                  ></a>
                </can>
              </v-col>
            </v-row>
            <dt-pagination @change="getResource" v-model="resources.meta"></dt-pagination>
          </template>

          <template v-else>
            <empty-state>
              <template v-slot:actions>
                <v-btn
                  :to="{name: 'threads.create'}"
                  class="mb-10"
                  color="primary"
                  exact
                  large
                  >
                  <v-icon small left>mdi-chat-plus-outline</v-icon>
                  <span v-text="trans('Add Discussion')"></span>
                </v-btn>
              </template>
            </empty-state>
          </template>
        </v-col>
      </v-row>
    </template>
    <!-- Items -->

    <v-card color="transparent" flat height="100"></v-card>
  </web-container>
</template>

<script>
import $api from './routes/api'
import Discussion from './Models/Discussion'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    SkeletonLoadingList: () => import('./cards/SkeletonLoadingList'),
  },

  computed: {
    ...mapGetters({
      toggletoolbar: 'toolbar/toolbar',
    }),
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },
    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },
  },

  data: () => ({
    resources: {
      loading: true,
      data: [],
    },
    categories: [
      {
        title: 'All threads',
        icon: 'mdi-note-outline'
      },
    ]
  }),

  methods: {
    ...mapActions({
      errorDialog: 'dialog/error',
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
    }),

    getResource (params = {}) {
      this.resources.loading = true

      axios.get(
        $api.list(), { params }
      ).then(response => {
        this.resources.data = response.data.data
        this.resources.links = response.data.links
        this.resources.meta = response.data.meta
      }).finally(() => { this.resources.loading = false })
    },

    askUserToPermanentlyDeleteResource (item) {
      this.showDialog({
        color: 'error',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to permanently delete the selected discussion.',
        text: ['Some data related to the account will still remain.', trans('Are you sure you want to permanently delete :name?', {name: item.title})],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: 'Permanently delete',
            color: 'error',
            callback: (dialog) => {
              this.loadDialog(true)
              this.deleteResource(item)
            }
          }
        }
      })
    },

    deleteResource (item) {
      item.loading = true
      axios.delete(
        $api.delete(item.id)
      ).then(response => {
        this.hideDialog()
        this.showSnackbar({
          text: trans('Discussion deleted successfully'),
        })
        item.loading = false
        this.getResource()
      })
    },
  },

  mounted () {
    this.getResource()
  },
}
</script>

