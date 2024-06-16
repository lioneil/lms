<template>
  <admin>
    <metatag :title="resource.data.title"></metatag>

    <template v-slot:appbar>
      <appbar-action-buttons
        :is-form-disabled="isFormDisabled"
        :is-loading="isLoading"
        :is-not-form-prestine="isNotFormPrestine"
        :shortkey-ctrl-is-pressed="shortkeyCtrlIsPressed"
        :submit-form="submitForm"
        @item:submit="submitForm"
        router-name="announcements.index"
        v-model="resource"
        >
        <template v-slot:text>
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'announcements.index' }, text: trans('Announcements') }">
      <template v-slot:title>
        <span v-text="trans('Edit Announcement')"></span>
      </template>
    </page-header>

    <validation-observer ref="updateform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        autocomplete="false"
        enctype="multipart/form-data"
        ref="updateform-form"
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
                          :label="trans('Title')"
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
                      <category-picker :url="categoryListUrl" v-model="resource.data.category_id"></category-picker>
                    </v-col>
                    <v-col cols="12">
                      <v-card :color="isDarkMode ? 'workspace' : 'white'" :class="{ 'dt-full-width': fullWidth }" flat>
                        <div :class="{ 'container': fullWidth }">
                          <v-card-title class="px-0">
                            <span v-text="trans(`Content`)"></span>
                            <v-spacer></v-spacer>
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on }">
                                <v-btn v-on="on" icon @click="toggleFullWidth">
                                  <v-icon v-html="fullWidth ? 'mdi-fullscreen-exit' : 'mdi-fullscreen'"></v-icon>
                                </v-btn>
                              </template>
                              <span v-html="fullWidth ? 'Exit fullscreen' : 'Fullscreen'"></span>
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
                                      <v-toolbar-title v-text="trans('Preview Content') "></v-toolbar-title>
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
                </v-card>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-row>
              <v-col cols="12">
                <!-- <template v-if="isFetchingResource">
                  <skeleton-icon class="mb-6"></skeleton-icon>
                </template>
                <v-card v-show="isFinishedFetchingResource" class="mb-6">
                  <v-card-title class="pb-0" v-text="trans('Photo')"></v-card-title>
                  <v-card-text class="text-center">
                      <upload-avatar name="photo" avatar="image_old" v-model="resource.data.photo"></upload-avatar>
                  </v-card-text>
                </v-card> -->

                <template v-if="isFetchingResource">
                  <skeleton-metainfo-card></skeleton-metainfo-card>
                </template>
                <metainfo-card v-show="isFinishedFetchingResource" :list="metaInfoCardList"></metainfo-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-form>
    </validation-observer>
    <v-card flat color="transparent" height="100"></v-card>
    <back-to-top></back-to-top>
  </admin>
</template>

<script>
import $api from './routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Announcement from './Models/Announcement'
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
    SkeletonIcon: () => import('./cards/SkeletonIcon'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
    isDarkMode () {
      return this.$vuetify.theme.isDark;
    },
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
    isInvalid () {
      return this.resource.isPrestine || this.progressbar.loading
    },
    isLoading () {
      return this.progressbar.loading
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
    resource: new Announcement,
    isValid: true,
    categoryListUrl: $api.category.list(),
    fullWidth: false,
    previewContent: false,
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
      setProgressStatus: 'progressbar/setProgressStatus',
      hideProgressbar: 'progressbar/hideProgressbar',
      showProgressbar: 'progressbar/showProgressbar',
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
      // # Shows the category missing error
      if(!this.resource.data.category_id) {
        const errors = {
          body: ['The category is required.']
        }
        this.showErrorbox({
          text: trans('The given data was invalid.'),
          errors: errors,
        })
        return
      }
      // # Shows the category missing error

      this.showProgressbar()
      this.hideAlertbox()
      e.preventDefault()

      axios.post(
        $api.update(this.resource.data.id),
        this.parseResourceData(this.resource.data), {
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
        this.showSnackbar({
          text: trans('Announcement updated successfully'),
        })

        this.showSuccessbox({
          text: trans(`Updated Announcement ${this.resource.data.title}`),
          buttons: {
            show: {
              code: 'announcements.index',
              to: { name: 'announcements.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Announcements'),
            },
            create: {
              code: 'announcements.create',
              to: { name: 'announcements.create', query: {from: this.$route.fullPath} },
              icon: 'mdi-bullhorn-outline',
              text: trans('Add New Announcement'),
            },
          },
        })

        this.$refs['updateform'].reset()
        this.resource.isPrestine = true
      }).catch(err => {
        if (err.response.status == Response.HTTP_UNPROCESSABLE_ENTITY) {
          let errorCount = size(err.response.data.errors)

          this.$refs['updateform'].setErrors(err.response.data.errors)
          this.showErrorbox({
            text: trans(err.response.data.message),
            errors: err.response.data.errors,
          })
        }
      }).finally(() => { this.hideProgressbar() })
    },

    getResource () {
      this.resource.loading = true
      this.showProgressbar()
      axios.get(
        $api.show(this.$route.params.id), {
          params: { materials: true }
        }).then(response => {
        this.resource.data = response.data.data
      }).finally(() => {
        this.resource.loading = false
        this.hideProgressbar()
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

        if (!this.resource.hasErrors && !this.isLoading) {
          this.hideAlertbox()
        }
      },
      deep: true,
    },
  },
}
</script>
