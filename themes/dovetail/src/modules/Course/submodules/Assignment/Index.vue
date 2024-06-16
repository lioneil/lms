<template>
  <admin>
    <metatag :title="trans('Assignments')"></metatag>

    <page-header></page-header>

    <v-card>
      <toolbar-menu
        :items.sync="tabletoolbar"
        bulk
        downloadable
        deletable
        @update:search="search"
        @update:delete="bulkDeleteResources"
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

            <!-- Action buttons -->
            <template v-slot:item.action="{ item }">
              <div class="text-no-wrap">
                <!-- Permanently Delete -->
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn @click="askUserToPermanentlyDeleteResource(item)" icon v-on="on">
                      <v-icon small>mdi-delete-forever-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans_choice('Permanently delete this index', 1) }}</span>
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
                large
                color="primary"
                exact
                :to="{name: 'categories.create'}">
                <v-icon small left>mdi-account-plus-outline</v-icon>
                {{ trans('Add Category') }}
              </v-btn>
            </template>
          </empty-state>
        </template>
      </v-slide-y-reverse-transition>
    </v-card>
  </admin>
</template>

<script>
import $api from '@/modules/Course/routes/api'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import man from '@/components/Icons/ManThrowingAwayPaperIcon.vue'
import { mapActions, mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
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
        { text: trans('Title'), align: 'left', value: 'title', class: 'text-no-wrap' },
        { text: trans('Course'), align: 'left', value: 'coursewareable_id', class: 'text-no-wrap' },
        { text: trans('Last Modified'), value: 'created_at', class: 'text-no-wrap' },
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

  mounted: function () {
    this.changeOptionsFromRouterQueries()
  },

  methods: {
    ...mapActions({
      errorDialog: 'dialog/error',
      hideDialog: 'dialog/hide',
      hideSnackbar: 'snackbar/hide',
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      showSnackbar: 'snackbar/show',
    }),

    load (val = true) {
      this.resource.loading = val
    },

    parseResourceData (item) {
      let data = clone(item)
      let formData = new FormData(this.$refs['addform-form'].$el)

      data = formData

      return data
    },

    parseErrors (errors) {
      this.form.setErrors(errors)
      errors = Object.values(errors).flat()
      this.resource.hasErrors = errors.length
      return errors
    },

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
      axios.get(this.api.assignment.list(), { params })
        .then(response => {
          this.resources = Object.assign({}, this.resources, response.data)
          this.resources.options = Object.assign(this.resources.options, response.data.meta, params)
          this.resources.loading = false
          this.$router.push({query: Object.assign({}, this.$route.query, params)}).catch(err => {})
        })
        .catch(err => {
          this.errorDialog({
            width: 400,
            buttons: { cancel: { show: false } },
            title: trans('Whoops! An error occured'),
            text: err.response.data.message,
          })
        })
        .finally(() => {
          this.resources.data.map(function (data) {
            return Object.assign(data, {loading: false})
          })
        })
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

    askUserToPermanentlyDeleteResource (item) {
      this.showDialog({
        color: 'error',
        illustration: man,
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to permanently delete the selected index.',
        text: ['Some data related to the account will still remain.', trans('Are you sure you want to permanently delete?', {name: item.name})],
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
        $api.category.delete(item.id)
      ).then(response => {
        item.active = false
        this.getPaginatedData(null)
        this.hideDialog()
        this.showSnackbar({
          text: trans_choice('Category successfully deleted', 1)
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

    bulkDeleteResources () {
      let selected = this.selected
      axios.delete(
        $api.category.delete(null), {
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
  },

  watch: {
    'resource.data': {
      handler (val) {
        this.resource.isPrestine = false
        this.resource.hasErrors = this.$refs.addform.flags.invalid
        if (!this.resource.hasErrors) {
          this.hideAlertbox()
        }
      },
      deep: true,
    },

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
