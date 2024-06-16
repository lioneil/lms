<template>
  <admin>
    <metatag :title="trans('All Courses')"></metatag>

    <page-header>
      <template v-slot:utilities>
        <router-link
          tag="a"
          class="dt-link t-d-none mr-4"
          exact
          :to="{name: 'courses.all'}"
          >
          <v-icon small left>mdi-eye-outline</v-icon>
          <span v-text="trans('View as Learner')"></span>
        </router-link>
        <can code="courses.trashed">
          <router-link
            :to="{name: 'courses.trashed', query: {from: $route.fullPath}}"
            class="dt-link t-d-none mr-4"
            exact
            tag="a"
            >
            <v-icon small left>mdi-delete-outline</v-icon>
            <span v-text="trans('Trashed Courses')"></span>
          </router-link>
        </can>
      </template>

      <template v-slot:action>
        <can code="courses.create">
          <div v-show="resourcesIsNotEmpty">
            <v-btn
              :block="$vuetify.breakpoint.smAndDown"
              :to="{ name: 'courses.create' }"
              color="primary"
              exact
              large
              >
              <v-icon small left>mdi-book-open-outline</v-icon>
              <span v-text="trans('Add Course')"></span>
            </v-btn>
          </div>
        </can>
      </template>
    </page-header>

    <!-- Items -->
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

            <template v-slot:item.title="{ item }">
              <avatar-and-title v-model="item"></avatar-and-title>
            </template>

            <template v-slot:item.category="{ item }">
              <category v-model="item"></category>
            </template>

            <template v-slot:item.published_at="{ item }">
              <published-chip
                v-if="item.published"
                :url="api.unpublish(item.id)"
                @published="handlePublished"
                >
                <template v-slot:context-title>
                  <span v-text="trans('Unpublish this course')"></span>
                </template>
                <template v-slot:context-text>
                  <span v-text="trans('Unpublishing will not let the users see this course and its lessons. Are you sure you want to proceed?')"></span>
                </template>
              </published-chip>

              <unpublished-chip
                v-if="item.unpublished"
                :url="api.publish(item.id)"
                @unpublished="handleUnpublished"
                >
                <template v-slot:context-title>
                  <span v-text="trans('Publish this course')"></span>
                </template>
                <template v-slot:context-text>
                  <span v-text="trans('Publishing will let the users see this course and its lessons. Are you sure you want to proceed?')"></span>
                </template>
              </unpublished-chip>
            </template>

            <template v-slot:item.updated_at="{ item }">
              <span class="text-no-wrap" v-text="item.modified"></span>
            </template>

            <template v-slot:item.action="{ item }">
              <action-buttons :details="false" name="courses" @item:destroy="askUserToDestroyCourse" v-model="item">
                <template v-slot:middle>
                  <!-- Single -->
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn
                        :to="{ name: 'courses.overview', params: {courseslug: item.slug}, query: {from: $route.fullPath} }"
                        icon
                        v-on="on"
                        >
                        <v-icon small>mdi-open-in-new</v-icon>
                      </v-btn>
                    </template>
                    <span v-text="trans('Show Overview')"></span>
                  </v-tooltip>
                  <!-- Single -->

                  <!-- Content -->
                  <can code="courses.edit">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          :to="{ name: 'contents.index', params: {id: item.id}, query: {from: $route.fullPath} }"
                          icon
                          v-on="on"
                          >
                          <v-icon small>mdi-file-document-edit-outline</v-icon>
                        </v-btn>
                      </template>
                      <span v-text="trans('Manage contents')"></span>
                    </v-tooltip>
                  </can>
                  <!-- Content -->
                </template>
              </action-buttons>
            </template>
          </v-data-table>
        </template>

        <template v-else>
          <empty-course-list></empty-course-list>
        </template>
      </v-slide-y-reverse-transition>
    </v-card>
    <!-- Items -->
  </admin>
</template>

<script>
import $api from './routes/api'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    AvatarAndTitle: () => import('./partials/AvatarAndTitle'),
    Category: () => import('./partials/Category'),
    EmptyCourseList: () => import('./partials/EmptyCourseList'),
  },

  computed: {
    ...mapGetters({
      toggletoolbar: 'toolbar/toolbar',
    }),
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },

    options () {
      return {
        per_page: this.resources.options.itemsPerPage,
        page: this.resources.options.page,
        sort: this.resources.options.sortBy[0] || undefined,
        order: this.resources.options.sortDesc[0] || false ? 'desc' : 'asc',
      }
    },

    selected () {
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
        { text: trans('Category'), align: 'center', value: 'category', class: 'text-no-wrap' },
        { text: trans('Status'), align: 'center', value: 'published_at', class: 'text-no-wrap' },
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

    handlePublished () {
      this.showSnackbar({
        text: trans('You have successfully unpublish the course'),
      })
      this.getPaginatedData()
    },

    handleUnpublished () {
      this.showSnackbar({
        text: trans('You have successfully publish the course'),
      })
      this.getPaginatedData()
    },

    getPaginatedData: function (params = null, caller = null) {
      params = Object.assign(params ? params : this.$route.query, { search: this.resources.search })
      this.resources.loading = true
      axios.get(this.api.list(), { params })
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

    bulkTrashResource () {
      let selected = this.selected
      axios.delete($api.destroy(null), { data: { id: selected } })
        .then(response => {
          this.getPaginatedData(null, 'bulkTrashResource')
          this.tabletoolbar.toggleTrash = false
          this.tabletoolbar.toggleBulkEdit = false
          this.hideDialog()
          this.showSnackbar({
            text: trans_choice('Course successfully moved to trash', this.tabletoolbar.bulkCount)
          })
        })
        .catch(err => {
          this.errorDialog({
            width: 400,
            buttons: { cancel: { show: false } },
            title: trans('Whoops! An error occured'),
            text: err.response.data.message,
          })
        })
    },

    askUserToDestroyCourse (item) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to move to trash the selected course.',
        text: ['Some data related to course will still remain.', trans('Are you sure you want to move :name to Trash?', {name: item.title})],
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
      axios.delete($api.destroy(item.id))
        .then(response => {
          item.active = false
          this.getPaginatedData(null, 'destroyResource')
          this.showSnackbar({
            text: trans_choice('Course successfully moved to trash', 1)
          })
          this.hideDialog()
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
          item.active = false
          item.loading = false
        })
    },
  },

  mounted () {
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
