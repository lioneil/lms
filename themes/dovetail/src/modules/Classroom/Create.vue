<template>
  <admin>
    <metatag :title="trans('Add Classroom')"></metatag>

    <template v-slot:appbar>
      <appbar-action-buttons
        :is-form-disabled="isFormDisabled"
        :is-loading="isLoading"
        :is-not-form-prestine="isNotFormPrestine"
        :shortkey-ctrl-is-pressed="shortkeyCtrlIsPressed"
        :submit-form="submitForm"
        router-name="classrooms.index"
        v-model="resource"
        @item:submit="submitForm"
        >
        <template v-slot:text>
          <span v-text="trans('Save')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'classrooms.index' }, text: trans('Classrooms') }">
      <template v-slot:title>
        <span v-text="trans('Add Classroom')"></span>
      </template>
    </page-header>

    <validation-observer ref="addform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        ref="addform-form"
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
                    <v-textarea
                      :label="trans('Description')"
                      auto-grow
                      class="dt-text-field"
                      name="description"
                      outlined
                    ></v-textarea>
                  </v-col>
                  <input type="hidden" name="code" :value="slugify(resource.data.name)">
                  <input type="hidden" name="user_id" :value="auth.id">
                </v-row>
              </v-card-text>

              <div class="d-flex mb-6">
                <v-divider></v-divider>
                <v-icon small color="muted" class="mx-3 mt-n2">mdi-book-open-outline</v-icon>
                <v-divider></v-divider>
              </div>
              <v-card-text class="pa-0">
                <h4 class="mb-5" v-text="trans('Courses')"></h4>
                <template v-if="coursesIsNotEmpty">
                  <treeview-field v-model="search"></treeview-field>
                  <course-treeview
                    description="text--ellipsis-1"
                    :items="resource.data.courses"
                    :search="search"
                    :selectable="true"
                    v-model="resource.data.selected"
                  ></course-treeview>

                  <input type="hidden" v-for="item in resource.data.selected" name="courses[]" :value="item">
                  <!-- <validation-provider vid="courses" name="courses" rules="required" v-slot="{ errors }">
                    <v-card-text :key="item" class="error--text" v-html="item" v-for="item in errors"></v-card-text>
                  </validation-provider> -->
                </template>

                <template v-if="coursesIsEmpty">
                  <v-card class="text-center" flat color="transparent">
                    <v-card-text style="filter: grayscale(0.8);">
                      <empty-icon class="primary--text" width="250" height="250"></empty-icon>
                    </v-card-text>
                    <v-card-text>
                      <slot name="text">
                        <p class="muted--text font-weight-bold mb-0" v-text="trans('No courses available')"></p>
                        <p class="muted--text" v-text="trans('Start creating a course by clicking Add Course button below.')"></p>

                        <v-btn
                          :to="{name: 'courses.create'}"
                          class="mt-6"
                          color="primary"
                          exact
                          large
                          >
                          <v-icon small left>mdi-book-open-outline</v-icon>
                          <span v-text="trans('Add Course')"></span>
                        </v-btn>
                      </slot>
                    </v-card-text>
                  </v-card>
                </template>

              </v-card-text>
            </v-card>
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
import Classroom from './Models/Classroom'
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

  components: {
    CourseTreeview: () => import('./partials/CourseTreeview'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
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
    coursesIsEmpty () {
      return !this.resource.data.courses
    },
    coursesIsNotEmpty () {
      return !isEmpty(this.resource.data.courses)
    },
    coursesIsEmpty () {
      return isEmpty(this.resource.data.courses)
    }
  },

  data: () => ({
    auth: $auth.getUser(),
    resource: new Classroom,
    search: null,
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

    getCourse () {
      axios.get(
        $api.courses.list()
      ).then(response => {
        this.resource.data.courses = Object.assign([], this.resource.data.courses, response.data.data)
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

    load (val = true) {
      this.resource.loading = val
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
        $api.store(), this.parseResourceData(this.resource.data))
        .then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Classroom created successfully'),
        })

        this.$router.push({
          name: 'classrooms.edit',
          params: {
            id: response.data.id
          },
          query: {
            success: true,
          },
        })

        this.showSuccessbox({
          text: trans('Created Classroom {name}', { name: response.data.name }),
          buttons: {
            show: {
              code: 'classrooms.index',
              to: { name: 'classrooms.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Classrooms'),
            },
            create: {
              code: 'classrooms.create',
              to: { name: 'classrooms.create' },
              icon: 'mdi-google-classroom',
              text: trans('Add New Classroom'),
            },
          },
        })
      }).catch(err => {
        this.form.setErrors(err.response.data.errors)
      }).finally(() => {
        this.load(false)
      })
    },
  },

  mounted () {
    this.getCourse()
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
