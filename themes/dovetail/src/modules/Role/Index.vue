<template>
  <admin>
    <metatag :title="trans('All Roles')"></metatag>

    <page-header>
      <template v-slot:utilities>
        <router-link tag="a" class="dt-link text-decoration-none mr-4" exact :to="{name: 'roles.trashed'}">
          <v-icon small left>mdi-delete-outline</v-icon>
          {{ trans('Trashed Roles') }}
        </router-link>
      </template>

      <template v-slot:action>
        <v-btn :block="$vuetify.breakpoint.smAndDown" large color="primary" exact :to="{ name: 'roles.create' }">
          <v-icon small left>mdi-shield-plus-outline</v-icon>
          {{ trans('Add Role') }}
        </v-btn>
      </template>
    </page-header>

    <!-- Data table -->
    <v-card>
      <toolbar-menu
        :items.sync="tabletoolbar"
        @update:search="search"
        @update:trash="bulkTrashResource"
        bulk
        downloadable
        trashable
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
                  <div v-for="i in 5" :key="i">
                    <skeleton-table></skeleton-table>
                  </div>
                </div>
              </v-slide-y-transition>
            </template>

            <!-- Name -->
              <template v-slot:item.name="{ item }">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <span class="mt-1" v-on="on"><router-link tag="a" exact :to="goToEditRolePage(item)" v-text="item.name" class="text-no-wrap t-d-none t-d-hover-lined"></router-link></span>
                  </template>
                  <span>{{ trans('Edit this role') }}</span>
                </v-tooltip>
              </template>
              <!-- Name -->

            <!-- Permissions -->
            <template v-slot:item.status="{ item }">
              <span class="text-no-wrap" :title="item.status">{{ trans(item.status) }}</span>
            </template>
            <!-- Permissions -->

            <!-- Modified -->
            <template v-slot:item.updated_at="{ item }">
              <span class="text-no-wrap" :title="item.updated_at">{{ trans(item.modified) }}</span>
            </template>
            <!-- Modified -->

            <!-- Action buttons -->
            <template v-slot:item.action="{ item }">
              <div class="text-no-wrap">
                <!-- Preview -->
                <can code="roles.show">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn :to="{name: 'roles.show', params: {id: item.id}}" icon v-on="on">
                        <v-icon small>mdi-open-in-new</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ trans('View details') }}</span>
                  </v-tooltip>
                </can>
                <!-- Preview -->
                <!-- Move to Trash -->
                <can code="roles.destroy">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn @click="askUserToDestroyRole(item)" icon v-on="on">
                        <v-icon small>mdi-delete-outline</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ trans('Move to trash') }}</span>
                  </v-tooltip>
                </can>
                <!-- Move to Trash -->
              </div>
            </template>
            <!-- Action buttons -->
          </v-data-table>
        </template>

        <template v-else>
          <empty-state class="mb-10">
            <template v-slot:actions>
              <v-btn
                :to="{name: 'roles.create'}"
                color="primary"
                exact
                large
                >
                <v-icon small left>mdi-account-plus-outline</v-icon>
                {{ trans('Add role') }}
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
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
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
      },
      meta: {},
      modes: {
        bulkedit: false,
      },
      selected: [],
      headers: [
        { text: trans('Role Name'), align: 'left', value: 'name', class: 'text-no-wrap' },
        { text: trans('Code'), align: 'left', value: 'code', class: 'text-no-wrap' },
        { text: trans('Permissions'), align: 'left', value: 'status', class: 'text-no-wrap' },
        { text: trans('Last Modified'), value: 'updated_at', class: 'text-no-wrap' },
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
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
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

    getPaginatedData: function (params = null, caller = null) {
      params = Object.assign(params ? params : this.$route.query, { search: this.resources.search })
      this.resources.loading = true
      axios.get(
        this.api.list(), { params }
      ).then(response => {
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

    goToEditRolePage (role) {
      return { name: 'roles.edit', params: { id: role.id, slug: role.rolename } }
    },

    search: debounce(function (event) {
      this.resources.search = event.srcElement.value || ''
      this.tabletoolbar.isSearching = false
      // if (this.resources.searching) {
        this.getPaginatedData(this.options, 'search')
        this.resources.searching = false
      // }
    }, 200),

    focusSearchBar () {
      this.$refs['tablesearch'].focus()
    },

    bulkTrashResource () {
      let selected = this.selected
      axios.delete(
        $api.destroy(null), { data: { id: selected } }
      ).then(response => {
        this.getPaginatedData(null, 'bulkTrashResource')
        this.tabletoolbar.toggleTrash = false
        this.tabletoolbar.toggleBulkEdit = false
        this.hideDialog()
        this.showSnackbar({
          text: trans_choice('Role successfully moved to trash', this.tabletoolbar.bulkCount)
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

    askUserToDestroyRole (item) {
      this.showDialog({
        color: 'warning',
        illustration: man,
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to move to trash the selected role.',
        text: ['The user will be signed out from the app. Some data related to the account like comments and files will still remain.', trans('Are you sure you want to move :name to Trash?', {name: item.name})],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: 'Move to Trash',
            color: 'warning',
            callback: (dialog) => {
              this.loadDialog(true)
              this.destroyResource(item)
            }
          }
        }
      })
    },

    destroyResource (item) {
      item.loading = true
      axios.delete(
        $api.destroy(item.id)
      ).then(response => {
        item.active = false
        this.getPaginatedData(null, 'destroyResource')
        this.showSnackbar({
          text: trans_choice('Role successfully moved to trash', 1)
        })
        this.hideDialog()
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
