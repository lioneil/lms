<template>
  <admin>
    <metatag :title="trans('Trashed Roles')"></metatag>

    <page-header :back="{ to: { name: 'roles.index' }, text: trans('Roles') }"></page-header>

    <!-- Data table -->
    <v-card>
      <toolbar-menu
        :items.sync="tabletoolbar"
        @update:delete="bulkDeleteResources"
        @update:restore="bulkRestoreResources"
        @update:search="search"
        bulk
        deletable
        restorable
        >
      </toolbar-menu>
      <v-slide-y-reverse-transition mode="out-in">
        <template v-if="resourcesIsNotEmpty">
          <v-data-table
            :headers="resources.headers"
            :items="resources.data"
            :loading="resources.loading"
            :options.sync="resources.options"
            :server-items-length="resources.meta.total"
            :show-select="tabletoolbar.toggleBulkEdit"
            @update:options="optionsChanged"
            color="primary"
            item-key="id"
            v-model="resources.selected"
            >
            <template v-slot:progress><span></span></template>

            <template v-slot:loading>
              <v-slide-y-transition mode="out-in">
                <div>
                  <div v-for="(j,i) in resources.options.itemsPerPage" :key="i">
                    <skeleton-table></skeleton-table>
                  </div>
                </div>
              </v-slide-y-transition>
            </template>

            <!-- Permissions -->
            <template v-slot:item.name="{ item }">
              <span class="muted--text" :title="item.name">{{ trans(item.name) }}</span>
            </template>
            <!-- Permissions -->

            <!-- Code -->
            <template v-slot:item.code="{ item }">
              <span class="muted--text" :title="item.code">{{ trans(item.code) }}</span>
            </template>
            <!-- Code -->

            <!-- Permissions -->
            <template v-slot:item.status="{ item }">
              <span class="muted--text" :title="item.status">{{ trans(item.status) }}</span>
            </template>
            <!-- Permissions -->

            <!-- Deleted -->
            <template v-slot:item.deleted_at="{ item }">
              <span class="text-no-wrap muted--text" :title="item.deleted_at">{{ trans(item.deleted) }}</span>
            </template>
            <!-- Deleted -->

            <!-- Action buttons -->
            <template v-slot:item.action="{ item }">
              <div class="text-no-wrap">
                <!-- Restore -->
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn @click="restoreResource(item)" icon v-on="on">
                      <v-icon class="mdi-spin" small v-if="item.loading">mdi-loading</v-icon>
                      <v-icon small v-else>mdi-restore</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans_choice('Restore this role', 1) }}</span>
                </v-tooltip>
                <!-- Restore -->
                <!-- Permanently Delete -->
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn @click="askUserToPermanentlyDeleteResource(item)" icon v-on="on">
                      <v-icon small>mdi-delete-forever-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans_choice('Permanently delete this role', 1) }}</span>
                </v-tooltip>
                <!-- Permanently Delete -->
              </div>
            </template>
            <!-- Action buttons -->
          </v-data-table>
        </template>

        <template v-else>
          <empty-state class="mb-10">
            <template v-slot:actions>
              <v-btn
                :to="{name: 'roles.index'}"
                color="primary"
                exact
                large
                >
                {{ trans('Go back to all roles') }}
              </v-btn>
            </template>
          </empty-state>
        </template>
      </v-slide-y-reverse-transition>
    </v-card>
    <!-- Data table -->
  </admin>
</template>

<script>
import $api from './routes/api'
import man from '@/components/Icons/ManThrowingAwayPaperIcon.vue'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import { mapActions } from 'vuex'

export default {
  computed: {
    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },

    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    options: function () {
      return {
        per_page: this.resources.options.itemsPerPage,
        page: this.resources.options.page,
        sort: this.resources.options.sortBy[0] || undefined,
        order: this.resources.options.sortDesc[0] || false ? 'desc' : 'asc',
      }
    },

    selected: function () {
      return this.resources.selected.map((item) => (item.id) )
    },
  },

  data: () => ({
    api: $api,

    resources: {
      loading: true,
      search: null,
      options: {
        page: 1,
        pageCount: 0,
        itemsPerPage: 10,
        sortDesc: [],
        sortBy: [],
        // rowsPerPage: [5, 10, 15, 20, 50, 100],
      },
      meta: {},
      modes: {
        bulkedit: false,
      },
      selected: [],
      headers: [
        { text: trans('Role Name'), align: 'left', value: 'name', class: 'text-no-wrap' },
        { text: trans('Code'), align: 'left', value: 'code', class: 'text-no-wrap' },
        { text: trans('Permissions'), value: 'status', class: 'text-no-wrap' },
        { text: trans('Date Deleted'), value: 'deleted_at', class: 'text-no-wrap' },
        { text: trans('Actions'), align: 'center', value: 'action', sortable: false, class: 'muted--text text-no-wrap' },
      ],
      data: []
    },

    tabletoolbar: {
      bulkCount: 0,
      isSearching: false,
      search: null,
      listGridView: false,
      toggleBulkEdit: false,
      toggleTrash: false,
      verticaldiv: false,
    },
  }),

  computed: {
    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },

    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    options: function () {
      return {
        per_page: this.resources.options.itemsPerPage,
        page: this.resources.options.page,
        sort: this.resources.options.sortBy[0] || undefined,
        order: this.resources.options.sortDesc[0] || false ? 'desc' : 'asc',
      }
    },

    selected: function () {
      return this.resources.selected.map((item) => (item.id) )
    },
  },

  mounted: function () {
    this.changeOptionsFromRouterQueries()
  },

  methods: {
    ...mapActions({
      errorDialog: 'dialog/error',
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
    }),

    changeOptionsFromRouterQueries () {
      this.options.per_page = this.$route.query.per_page
      this.options.page = parseInt(this.$route.query.page)
      this.options.search = this.$route.query.search
      this.resources.search = this.options.search
      this.tabletoolbar.search = this.options.search
    },

    optionsChanged (options) {
      this.getPaginatedData(this.options)
    },

    getPaginatedData: function (params = null) {
      params = Object.assign(params ? params : this.$route.query, { search: this.resources.search })
      this.resources.loading = true
      axios.get(
        this.api.trashed(), {
          params
      }).then(response => {
        this.resources = Object.assign({}, this.resources, response.data)
        this.resources.options = Object.assign(this.resources.options, response.data.meta, params)
        this.resources.loading = false
        this.$router.push({query: Object.assign({}, this.$route.query, params)}).catch(err => {})
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => {
        this.resources.data.map(function (data) {
          return Object.assign(data, {loading: false})
        })
      })
    },

    search: debounce(function (event) {
      this.resources.search = event.srcElement.value || ''
      this.tabletoolbar.isSearching = false
      // if (this.resources.searching) {
        this.getPaginatedData(this.options)
        this.resources.searching = false
      // }
    }, 200),

    focusSearchBar () {
      this.$refs['tablesearch'].focus()
    },

    bulkRestoreResources () {
      let selected = this.selected

      axios.patch(
        $api.restore(null), {
          id: selected
      }).then(response => {
        this.getPaginatedData(null)
        this.tabletoolbar.toggleTrash = false
        this.tabletoolbar.toggleBulkEdit = false
        this.hideDialog()
        this.showSnackbar({
          text: trans_choice('Role successfully restored', this.tabletoolbar.bulkCount)
        })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      })
    },

    bulkDeleteResources () {
      let selected = this.selected
      axios.delete(
        $api.delete(null), {
          data: { id: selected }
      }).then(response => {
        this.getPaginatedData(null)
        this.tabletoolbar.toggleTrash = false
        this.tabletoolbar.toggleBulkEdit = false
        this.hideDialog()
        this.showSnackbar({
          text: trans_choice('Items permanently deleted', this.tabletoolbar.bulkCount)
        })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      })
    },

    restoreResource (item) {
      item.loading = true
      axios.patch(
        $api.restore(item.id)
      ).then(response => {
        this.getPaginatedData(null)
        this.showSnackbar({
          text: trans_choice('Role successfully restored', 1)
        })
      })
    },

    askUserToPermanentlyDeleteResource (item) {
      this.showDialog({
        color: 'error',
        illustration: man,
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to permanently delete the selected role.',
        text: ['The role will be signed out from the app. Some data related to the account like comments and files will still remain.', trans('Are you sure you want to permanently delete?', {name: item.name})],
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
        item.active = false
        this.getPaginatedData(null)
        this.hideDialog()
        this.showSnackbar({
          text: trans_choice('Role successfully deleted', 1)
        })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => {
        item.active = false
        item.loading = false
      })
    },
  },

  mounted: function () {
    this.changeOptionsFromRouterQueries()
  },

  watch: {
    'resources.search': function (val) {
      this.resources.searching = true
    },

    'resources.selected': function (val) {
      this.tabletoolbar.bulkCount = val.length
    },

    'tabletoolbar.toggleBulkEdit': function (val) {
      if (!val) {
        this.resources.selected = []
      }
    }
  },
}
</script>
