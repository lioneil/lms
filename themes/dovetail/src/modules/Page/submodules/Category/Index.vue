<template>
  <admin>
    <metatag :title="trans('Categories')"></metatag>

    <page-header></page-header>

    <v-row>
      <v-col cols="12" md="4">
        <validation-observer
          ref="addform"
          v-slot="{ handleSubmit, errors, invalid, passed }"
          >
          <v-form
            :disabled="isLoading"
            autocomplete="false"
            ref="addform-form"
            v-on:submit.prevent="handleSubmit(submit($event))"
            >
            <button ref="submit-button" type="submit" class="d-none"></button>

            <!-- Alertbox -->
            <alertbox></alertbox>
            <!-- Alertbox -->

            <v-card flat color="transparent">
              <v-card-title class="pa-0 mb-3 mt-n3" v-text="trans('New Category')"></v-card-title>
              <v-card-text class="pa-0">
                <v-row>
                  <v-col cols="12">
                    <validation-provider vid="name" :name="trans('name')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :error-messages="errors"
                        :label="trans('Name')"
                        autofocus
                        class="dt-text-field"
                        name="name"
                        outlined
                        v-model="resource.data.name"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      :label="trans('Alias')"
                      class="dt-text-field"
                      name="alias"
                      outlined
                      v-model="resource.data.alias"
                      >
                    </v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      :label="trans('Description')"
                      auto-grow
                      class="dt-text-field"
                      name="description"
                      outlined
                    ></v-textarea>
                  </v-col>
                  <input type="hidden" name="code" :value="slugify(resource.data.name || '')">
                  <input type="hidden" name="type" :value="resource.data.type">
                  <input type="hidden" name="user_id" :value="auth.id">

                  <v-col cols="12">
                    <v-badge
                      bordered
                      bottom
                      class="dt-badge dt-badge-full"
                      color="dark"
                      content="s"
                      offset-x="20"
                      offset-y="20"
                      tile
                      transition="fade-transition"
                      v-model="shortkeyCtrlIsPressed"
                      >
                      <v-btn
                        :disabled="isFormDisabled"
                        :loading="isLoading"
                        @click.prevent="submitForm"
                        @shortkey="submitForm"
                        color="primary"
                        x-large
                        ref="submit-button-main"
                        type="submit"
                        v-shortkey.once="['ctrl', 's']"
                        block
                        >
                        <span v-text="trans('Save')"></span>
                      </v-btn>
                    </v-badge>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-form>
        </validation-observer>
      </v-col>

      <v-col cols="12" md="8">
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

                <!-- Name -->
                <template v-slot:item.name="{ item }">
                  <router-link class="t-d-none" tag="a" exact :to="{ name: 'pages.categories.edit', params: {id: item.id} }">
                    <div class="d-flex align-center">
                      <span class="text-no-wrap t-d-hover-lined" v-text="trans(item.name)"></span>
                    </div>
                  </router-link>
                </template>
                <!-- Name -->

                <!-- Modified -->
                <template v-slot:item.created_at="{ item }">
                  <span class="text-no-wrap" v-text="item.modified"></span>
                </template>
                <!-- Modified -->

                <!-- Action buttons -->
                <template v-slot:item.action="{ item }">
                  <action-buttons
                    @item:permanentdelete="askUserToPermanentlyDeleteResource"
                    deleted
                    v-model="item"
                  ></action-buttons>
                </template>
                <!-- Action buttons -->
              </v-data-table>
            </template>

            <template v-else>
              <empty-state></empty-state>
            </template>
          </v-slide-y-reverse-transition>
        </v-card>
      </v-col>
    </v-row>
  </admin>
</template>

<script>
import $api from '@/modules/Page/routes/api'
import $auth from '@/core/Auth/auth'
import Category from './Models/Category'
import clone from 'lodash/clone'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import { mapActions, mapGetters } from 'vuex'

export default {
  beforeRouteLeave (to, from, next) {
    if (this.isFormPrestine) {
      next()
    } else {
      this.askUserBeforeNavigatingAway(next)
    }
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
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
    selected () {
      return this.resources.selected.map((item) => (item.id) )
    },
    isInvalid () {
      return this.resource.isPrestine || this.resource.loading
    },
    isLoading () {
      return this.resource.loading
    },
    isFormDisabled () {
      return this.isInvalid || this.resource.isPrestine
    },
    isNotFormDisabled () {
      return !this.isFormDisabled
    },
    isFormPrestine () {
      return this.resource.isPrestine
    },
    isNotFormPrestine () {
      return !this.isFormPrestine
    },
    form () {
      return this.$refs['addform']
    },
  },

  data: () => ({
    auth: $auth.getUser(),
    resource: new Category,
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
        { text: trans('Name'), align: 'left', value: 'name', class: 'text-no-wrap' },
        { text: trans('Alias'), align: 'left', value: 'alias', class: 'text-no-wrap' },
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

  mounted () {
    this.changeOptionsFromRouterQueries()
  },

  methods: {
    ...mapActions({
      showAlertbox: 'alertbox/show',
      hideAlertbox: 'alertbox/hide',
      errorDialog: 'dialog/error',
      hideDialog: 'dialog/hide',
      hideSnackbar: 'snackbar/hide',
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      showSnackbar: 'snackbar/show',
    }),

    askUserBeforeNavigatingAway (next) {
      this.showDialog({
        illustration: () => import('@/components/Icons/WorkingDeveloperIcon.vue'),
        title: trans('Unsaved changes will be lost'),
        text: trans('You have unsaved changes on this page. If you navigate away from this page, data will not be recovered.'),
        buttons: {
          cancel: {
            text: trans('Go Back'),
            callback: () => {
              this.hideDialog()
            },
          },
          action: {
            text: trans('Discard'),
            callback: () => {
              next()
              this.hideDialog()
            },
          },
        }
      })
    },

    askUserToDiscardUnsavedChanges () {
      this.showDialog({
        illustration: () => import('@/components/Icons/WorkingDeveloperIcon.vue'),
        title: trans('Discard changes?'),
        text: trans('You have unsaved changes on this page. If you navigate away from this page, data will not be recovered.'),
        buttons: {
          cancel: {
            text: trans('Cancel'),
            callback: () => {
              this.hideDialog()
            },
          },
          action: {
            text: trans('Discard'),
            callback: () => {
              this.resource.isPrestine = true
              this.hideDialog()
              this.$router.replace({ name: 'pages.categories.index' })
            },
          },
        }
      })
    },

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

    submitForm () {
      if (this.isNotFormDisabled) {
        this.$refs['submit-button'].click()
        window.scrollTo({
          top: 0,
          left: 0,
          behavior: 'smooth',
        })
      }
    },

    submit (e) {
      this.load()
      this.hideAlertbox()
      e.preventDefault()

      axios.post(
        $api.category.store(), this.parseResourceData(this.resource.data)).then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Category created successfully'),
        })

        this.getPaginatedData({page: 1})
        this.$refs['addform-form'].reset()

      }).catch(err => {
        this.form.setErrors(err.response.data.errors)
      }).finally(() => {
        this.load(false)
      })
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
      axios.get(this.api.category.list(), { params })
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
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to permanently delete the selected category.',
        text: ['Some data related to the account will still remain.', trans('Are you sure you want to permanently delete :name?', {name: item.name})],
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
