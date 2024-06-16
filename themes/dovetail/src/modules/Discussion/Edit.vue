<template>
  <web-container>
    <metatag :title="resource.data.title"></metatag>

    <template v-slot:navbar>
      <appbar-action-buttons
        :is-form-disabled="isFormDisabled"
        :is-loading="isLoading"
        :is-not-form-prestine="isNotFormPrestine"
        :shortkey-ctrl-is-pressed="shortkeyCtrlIsPressed"
        :submit-form="submitForm"
        router-name="threads.all"
        v-model="resource"
        @item:submit="submitForm"
        >
        <template v-slot:text>
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'threads.all' }, text: trans('Discussions') }">
      <template v-slot:title>
        <span v-text="trans('Edit Discussion')"></span>
      </template>
    </page-header>

    <validation-observer ref="updateform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        ref="updateform-form"
        autocomplete="false"
        v-on:submit.prevent="handleSubmit(submit($event))"
        >
        <button ref="submit-button" type="submit" class="d-none"></button>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <v-row>
          <v-col cols="12" md="9">
            <v-card flat color="transparent">
              <v-card-text class="pa-0">
                <template v-if="isFetchingResource">
                  <skeleton-edit></skeleton-edit>
                </template>

                <v-card flat color="transparent" v-show="isFinishedFetchingResource">
                  <v-row>
                    <v-col cols="12">
                      <validation-provider vid="title" :name="trans('title')" rules="required" v-slot="{ errors }">
                        <v-text-field
                          :error-messages="errors"
                          :label="trans('Discussion Title')"
                          autofocus
                          class="dt-text-field"
                          name="title"
                          outlined
                          v-model="resource.data.title"
                          >
                        </v-text-field>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                      <v-card
                        :class="{ 'dt-full-width': fullWidth }"
                        :color="isDark ? 'workspace' : 'white'"
                        flat
                        >
                        <div :class="{ 'container': fullWidth }">
                          <v-card-title class="px-0">
                            <span v-text="trans(`What's on your mind?`)"></span>
                            <v-spacer></v-spacer>
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on }">
                                <v-btn v-on="on" icon @click="toggleFullWidth">
                                  <v-icon v-html="fullWidth ? 'mdi-fullscreen-exit' : 'mdi-fullscreen'"></v-icon>
                                </v-btn>
                              </template>
                              <span v-text="fullWidth ? 'Exit fullscreen' : 'Fullscreen'"></span>
                            </v-tooltip>

                            <v-divider vertical class="mx-3"></v-divider>

                            <v-tooltip bottom>
                              <template v-slot:activator="{ on }">
                                <v-btn
                                  @click="previewContent = true"
                                  icon
                                  text
                                  v-on="on"
                                  >
                                  <v-icon>mdi-monitor-eye</v-icon>
                                </v-btn>
                              </template>
                              <span v-text="trans('Preview Content')"></span>
                            </v-tooltip>
                          </v-card-title>

                          <v-card-text class="pa-0">
                            <dt-editor :class="fullWidth ? 'fullwidth' : null" v-model="resource.data.body" name="body" :url="api.upload()"></dt-editor>
                            <!-- Preview -->
                            <v-dialog v-model="previewContent" fullscreen hide-overlay transition="dialog-bottom-transition">
                              <v-card flat tile>
                                <v-toolbar elevation="1" color="bar">
                                  <v-container>
                                    <div class="d-flex justify-space-between align-center">
                                      <v-toolbar-title v-text="trans('Preview Content')"></v-toolbar-title>
                                      <v-spacer></v-spacer>
                                      <v-btn icon @click="previewContent = false">
                                        <v-icon>mdi-close</v-icon>
                                      </v-btn>
                                    </div>
                                  </v-container>
                                </v-toolbar>
                                <v-container>
                                  <v-row>
                                    <v-col cols="12">
                                      <div class="ck-content" v-html="resource.data.body"></div>
                                    </v-col>
                                  </v-row>
                                </v-container>
                              </v-card>
                            </v-dialog>
                            <!-- Preview -->
                          </v-card-text>
                        </div>
                      </v-card>
                    </v-col>
                  </v-row>
                  <input type="hidden" name="slug" :value="slugify(resource.data.title)">
                  <input type="hidden" name="user_id" :value="auth.id">
                  <input type="hidden" name="type" :value="resource.data.type">
                </v-card>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-row>
              <v-col cols="12">
                <template v-if="isFetchingResource">
                  <skeleton-category-card class="mb-6"></skeleton-category-card>
                  <skeleton-metainfo-card class="mb-6"></skeleton-metainfo-card>
                </template>
                <v-card v-show="isFinishedFetchingResource" class="mb-6">
                  <v-card-text class="text-center">
                    <typing-icon height="120" class="primary--text mb-3"></typing-icon>
                    <category-picker :url="categoryListUrl" v-model="resource.data.category_id"></category-picker>
                  </v-card-text>
                </v-card>
                <metainfo-card v-show="isFinishedFetchingResource" :list="metaInfoCardList"></metainfo-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-form>
    </validation-observer>

    <dialogbox></dialogbox>
  </web-container>
</template>

<script>
import $api from './routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Discussion from './Models/Discussion'
import size from 'lodash/size'
import { mapActions, mapGetters } from 'vuex'

export default {
  beforeRouteLeave (to, from, next) {
    if (this.resource.isPrestine) {
      next()
    } else {
      this.askUserBeforeNavigatingAway(next)
    }
  },

  components: {
    SkeletonEdit: () => import('./cards/SkeletonEdit'),
    SkeletonCategoryCard: () => import('./cards/SkeletonCategoryCard'),
  },

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isDark () {
      return this.$vuetify.theme.isDark
    },
    isInvalid () {
      return this.resource.isPrestine || this.resource.loading
    },
    isLoading () {
      return this.resource.loading
    },
    isFetchingResource () {
      return this.resource.loading
    },
    isFinishedFetchingResource () {
      return !this.resource.loading
    },
    isFormDisabled () {
      return this.isInvalid || this.resource.isPrestine
    },
    isFormPrestine () {
      return this.resource.isPrestine
    },
    isNotFormPrestine () {
      return !this.isFormPrestine
    },
    metaInfoCardList () {
      return [
        { icon: 'mdi-calendar', text: trans('Created :date', { date: this.resource.data.created }) },
        { icon: 'mdi-calendar-edit', text: trans('Modified :date', { date: this.resource.data.modified }) },
        { icon: 'mdi-account-outline', text: trans('Created by :name', { name: this.resource.data.author }) },
      ]
    },
  },

  data: () => ({
    api: $api,
    auth: $auth.getUser(),
    categoryListUrl: $api.category.list(),
    fullWidth: false,
    isValid: true,
    lockable: true,
    previewContent: false,
    resource: new Discussion,
  }),

  methods: {
    ...mapActions({
      hideAlertbox: 'alertbox/hide',
      hideDialog: 'dialog/hide',
      hideErrorbox: 'errorbox/hide',
      hideSnackbar: 'snackbar/hide',
      hideSuccessbox: 'successbox/hide',
      showAlertbox: 'alertbox/show',
      showDialog: 'dialog/show',
      showErrorbox: 'errorbox/show',
      showSnackbar: 'snackbar/show',
      showSuccessbox: 'successbox/show',
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

    parseResourceData (item) {
      let data = clone(item)

      let formData = new FormData(this.$refs['updateform-form'].$el)

      formData.append('_method', 'put')

      data = formData

      return data
    },

    load (val = true) {
      this.resource.loading = val
    },

    parseErrors (errors) {
      this.$refs['updateform'].setErrors(errors)
      errors = Object.values(errors).flat()
      this.resource.hasErrors = errors.length
      return this.resource.errors
    },

    getParseErrors (errors) {
      errors = Object.values(errors).flat()
      this.resource.hasErrors = errors.length
      return errors
    },

    submitForm () {
      if (!this.isFormDisabled) {
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

      // # Shows the body missing error
      if(!this.resource.data.body) {
        const errors = {
          body: ['The message is required.']
        }
        if(!this.resource.data.title)
          errors.title = ['The title field is required.']
        this.showErrorbox({
          text: trans('The given data was invalid.'),
          errors: errors,
        })
        return
      }
      // # Shows the body missing error

      axios.post(
        $api.update(this.resource.data.id),
        this.parseResourceData(this.resource.data))
        .then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Discussion updated successfully'),
        })

        this.showSuccessbox({
          text: trans(`Updated Discussion ${this.resource.data.title}`),
          buttons: {
            show: {
              code: 'threads.index',
              to: { name: 'threads.all' },
              icon: 'mdi-open-in-new',
              text: trans('View All Discussions'),
            },
            create: {
              code: 'threads.create',
              to: { name: 'threads.create', query: {from: this.$route.fullPath} },
              icon: 'mdi-chat-plus-outline',
              text: trans('Add New Discussion'),
            },
          },
        })

        // this.$refs['updateform'].reset()
        // this.resource.isPrestine = true
      }).catch(err => {
        // this.form.setErrors(err.response.data.errors)
        this.$refs['addform'].setErrors(err.response.data.errors)
        // # Shows the title instead of a slug on error
        if(err.response.data.errors.slug) {
          delete err.response.data.errors.title
          var newString = err.response.data.errors.slug[0].replace(/slug/g, "title")
          err.response.data.errors.slug[0] = newString
        }
        // # Shows the title instead of a slug on error
        this.showErrorbox({
          text: trans(err.response.data.message),
          errors: err.response.data.errors,
        })
      }).finally(() => { this.load(false) })
    },

    getResource: function () {
      this.resource.loading = true
      axios.get(
        $api.show(this.$route.params.id)
      ).then(response => {
        this.resource.data = response.data.data
      }).finally(() => {
        this.resource.loading = false
        this.resource.isPrestine = true
      })
    },

    toggleFullWidth () {
      this.fullWidth = ! this.fullWidth
    }
  },

  mounted () {
    this.getResource()
  },

  watch: {
    'resource.data': {
      handler (val) {
        this.resource.isPrestine = false
        this.resource.hasErrors = this.$refs.updateform.flags.invalid

        if (!this.resource.hasErrors) {
          this.hideAlertbox()
        }
      },
      deep: true,
    },
  },
}
</script>
