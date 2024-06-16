<template>
  <admin>
    <metatag :title="trans('Add Course')"></metatag>

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
          <span v-text="trans('Save')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'courses.index' }, text: trans('Courses') }">
      <template v-slot:title>
        <span v-text="trans('Add Course')"></span>
      </template>
    </page-header>

    <validation-observer ref="addform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        ref="addform-form"
        autocomplete="false"
        v-on:submit.prevent="handleSubmit(submit($event))"
        enctype="multipart/form-data"
        >
        <button ref="submit-button" type="submit" class="d-none"></button>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <v-row>
          <v-col cols="12" lg="9">
            <v-card flat color="transparent">
              <v-card-text class="pa-0">
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
                    <validation-provider vid="code" :name="trans('code')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :error-messages="errors"
                        :label="trans('Course Code')"
                        class="dt-text-field"
                        hint="E.g. CS10"
                        name="code"
                        outlined
                        v-model="resource.data.code"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12">
                    <category-picker class="mb-6" :url="categoryListUrl" v-model="resource.data.category_id"></category-picker>
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
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12">
                    <v-card flat color="transparent" class="mb-6">
                      <v-card-title class="pa-0" v-text="trans('Materials')"></v-card-title>
                      <v-card-text class="pa-0">
                        <material-repeater v-model="resource.data.materials"></material-repeater>
                      </v-card-text>
                    </v-card>
                  </v-col>
                  <input type="hidden" name="slug" :value="slugify(resource.data.title)">
                  <input type="hidden" name="user_id" :value="auth.id">
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" lg="3">
            <v-row>
              <v-col cols="12">
                <v-card class="mb-6">
                  <v-card-title class="pb-0" v-text="trans('Photo')"></v-card-title>
                  <v-card-text class="text-center">
                    <validation-provider vid="image" :name="trans('image')" rules="required" v-slot="{ errors }">
                      <upload-avatar :loading="isLoading" name="image" avatar="image_old" v-model="resource.data.image"></upload-avatar>
                      <div class="error--text mt-2"><small v-text="errors.join()"></small></div>
                    </validation-provider>
                  </v-card-text>
                </v-card>

                <!-- Lockable -->
                <validation-provider vid="metadata[lockable]" :name="trans('lockable')" v-slot="{ errors }">
                  <radio-card clas="mb-6">
                    <template v-slot:illustration>
                      <v-card-text>
                        <lock-icon height="150" class="primary--text"></lock-icon>
                      </v-card-text>
                    </template>
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
                    <template v-slot:radio>
                      <v-radio-group hide-details v-model="resource.data.metadata.lockable" row mandatory>
                        <v-radio label="Lockable" name="metadata[lockable]" value="true" class="mb-3"></v-radio>
                        <v-radio label="Non Lockable" name="metadata[lockable]" value="false"></v-radio>
                      </v-radio-group>
                    </template>
                  </radio-card>
                  </div>
                </validation-provider>
                <!-- Lockable -->
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
import { mapActions, mapGetters } from 'vuex'

export default {
  beforeRouteLeave (to, from, next) {
    if (this.isFormPrestine) {
      next()
    } else {
      this.askUserBeforeNavigatingAway(next)
    }
  },

  components: {
    MaterialRepeater: () => import('./partials/MaterialRepeater'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
    isInvalid () {
      return this.resource.isPrestine || this.progressbar.loading
    },
    isLoading () {
      return this.progressbar.loading
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
    resource: new Course,
    categoryListUrl: $api.category.list(),
    tagListUrl: $api.tag.list(),
  }),

  methods: {
    ...mapActions({
      showAlertbox: 'alertbox/show',
      hideAlertbox: 'alertbox/hide',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
      hideSnackbar: 'snackbar/hide',
      showSuccessbox: 'successbox/show',
      hideSuccessbox: 'successbox/hide',
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
      this.showProgressbar()
      this.hideAlertbox()
      e.preventDefault()

      axios.post(
        $api.store(), this.parseResourceData(this.resource.data), {
          headers: {'Content-Type': 'multipart/form-data'},
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Course created successfully'),
        })

        this.$router.push({
          name: 'courses.edit',
          params: {
            id: response.data.id
          },
          query: {
            success: true,
          },
        })

        this.showSuccessbox({
          text: trans('Created Course {name}', { name: response.data.title }),
          buttons: {
            show: {
              code: 'courses.index',
              to: { name: 'courses.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Courses'),
            },
            create: {
              code: 'courses.create',
              to: { name: 'courses.create' },
              icon: 'mdi-book-open-outline',
              text: trans('Add New Course'),
            },
          },
        })
      }).catch(err => {
        this.form.setErrors(err.response.data.errors)
      }).finally(() => {
        this.hideProgressbar()
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
  },
}
</script>
