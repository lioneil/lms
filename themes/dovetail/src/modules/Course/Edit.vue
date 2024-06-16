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
        router-name="courses.index"
        v-model="resource"
        @item:submit="submitForm"
        >
        <template v-slot:text>
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'courses.index' }, text: trans('Courses') }">
      <template v-slot:title>
        <span v-text="trans('Edit Course')"></span>
      </template>
      <template v-slot:action>
        <v-btn
          :block="isMobile"
          :to="{ name: 'contents.index', query: {from: $route.fullPath} }"
          color="primary"
          exact
          large
          text
          >
          <v-icon small left>mdi-file-document-edit-outline</v-icon>
          {{ trans('Add/Manage Course Content') }}
        </v-btn>
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
          <v-col cols="12" lg="9">
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
                      <validation-provider vid="subtitle" :name="trans('subtitle')" rules="required" v-slot="{ errors }">
                        <v-text-field
                          :error-messages="errors"
                          :label="trans('Subtitle')"
                          class="dt-text-field"
                          name="subtitle"
                          outlined
                          v-model="resource.data.subtitle"
                          >
                        </v-text-field>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                      <validation-provider vid="code" rules="required" :name="trans('code')" v-slot="{ errors }">
                        <v-text-field
                          :error-messages="errors"
                          :label="trans('Code')"
                          class="dt-text-field"
                          name="code"
                          outlined
                          v-model="resource.data.code"
                        ></v-text-field>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                      <category-picker :url="categoryListUrl" v-model="resource.data.category_id"></category-picker>
                    </v-col>
                    <v-col cols="12">
                      <tag-picker :url="tagListUrl" v-model="resource.data.tags"></tag-picker>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        :label="trans('Description')"
                        auto-grow
                        class="dt-text-field"
                        name="description"
                        outlined
                        v-model="resource.data.description"
                      ></v-textarea>
                    </v-col>
                    <v-col cols="12">
                      <v-col cols="12">
                        <v-card flat color="transparent" class="mb-6">
                          <v-card-title class="px-0" v-text="trans('Materials')"></v-card-title>
                          <v-card-text class="pa-0">
                            <material-repeater :has-old="true" v-model="resource.data.materials"></material-repeater>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-col>
                  </v-row>
                  <input type="hidden" name="slug" :value="slugify(resource.data.title)">
                  <input type="hidden" name="user_id" :value="auth.id">
                </v-card>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" lg="3">
            <v-row>
              <v-col cols="12">
                <template v-if="isFetchingResource">
                  <skeleton-icon class="mb-6"></skeleton-icon>
                </template>
                <v-card v-show="isFinishedFetchingResource" class="mb-6">
                  <v-card-title class="pb-0">{{ __('Photo') }}</v-card-title>
                  <v-card-text class="text-center">
                    <validation-provider vid="image" :name="trans('image')" rules="required" v-slot="{ errors }">
                      <upload-avatar name="image" avatar="image_old" v-model="resource.data.image"></upload-avatar>
                      <div class="error--text mt-2"><small v-text="errors.join()"></small></div>
                    </validation-provider>
                  </v-card-text>
                </v-card>

                <!-- Publish -->
                <v-card class="mb-6">
                  <v-card-title v-text="trans('Publish Date')"></v-card-title>
                  <v-card-text>
                    <publish-icon height="150" class="mb-10 primary--text"></publish-icon>
                    <date-time-picker
                      :icon="'mdi-arrow-top-right'"
                      :label="trans('Publish date')"
                      name="published_at"
                      v-model="resource.data.published_at"
                    ></date-time-picker>
                  </v-card-text>
                </v-card>
                <!-- Publish -->

                <!-- Lockable -->
                <template v-if="isFetchingResource">
                  <skeleton-toggle-card class="mb-6"></skeleton-toggle-card>
                </template>
                <radio-card v-show="isFinishedFetchingResource" class="mb-6">
                  <template v-slot:title>
                    <v-card-title class="pb-0">
                      <h4 v-text="trans('Course Lock')"></h4>
                      <v-spacer></v-spacer>
                      <v-menu
                        :close-on-content-click="false"
                        max-width="200"
                        offset-y
                        open-on-hover
                        >
                        <template v-slot:activator="{ on }">
                          <v-icon small color="muted" v-on="on">mdi-help-circle-outline</v-icon>
                        </template>
                        <v-card>
                          <v-card-text>
                            <p class="body-2" v-html="trans('A <code>lockable</code> course will let students view the lessons in order, unlocking the next course uppon finishing the current lesson.')"></p>
                            <p class="body-2" v-html="trans('A <code>non-lockable</code> course will allow students to view the lessons in any order.')"></p>
                          </v-card-text>
                        </v-card>
                    </v-menu>
                    </v-card-title>
                  </template>
                  <template v-slot:illustration>
                    <!-- <v-card-text> -->
                      <lock-icon height="150" class="mt-10 primary--text"></lock-icon>
                    <!-- </v-card-text> -->
                  </template>
                  <template v-slot:radio>
                    <v-radio-group hide-details v-model="resource.data.metadata.lockable" row mandatory>
                      <v-radio label="Lockable" name="metadata[lockable]" value="true" class="mb-3"></v-radio>
                      <v-radio label="Not Lockable" name="metadata[lockable]" value="false"></v-radio>
                    </v-radio-group>
                  </template>
                </radio-card>
                <!-- Lockable -->

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
import Course from './Models/Course'
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
    MaterialRepeater: () => import('./partials/MaterialRepeater'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
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
    auth: $auth.getUser(),
    resource: new Course,
    isValid: true,
    categoryListUrl: $api.category.list(),
    tagListUrl: $api.tag.list(),
    lockable: true,
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
          text: trans('Course updated successfully'),
        })

        this.showSuccessbox({
          text: trans('Updated Course {name}', { name: this.resource.data.title }),
          buttons: {
            show: {
              code: 'courses.index',
              to: { name: 'courses.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Courses'),
            },
            create: {
              code: 'courses.create',
              to: { name: 'courses.create', query: {from: this.$route.fullPath} },
              icon: 'mdi-book-open-outline',
              text: trans('Add New Course'),
            },
            utility: {
              code: 'courses.create',
              to: { name: 'contents.index', query: {from: this.$route.fullPath} },
              icon: 'mdi-file-document-outline',
              text: trans('Add Course Content'),
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
