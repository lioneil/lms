<template>
  <admin>
    <metatag :title="resource.data.name"></metatag>

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
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'classrooms.index' }, text: trans('Classrooms') }">
      <template v-slot:title>
        <span v-text="trans('Edit Classroom')"></span>
      </template>
    </page-header>

    <validation-observer ref="updateform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        autocomplete="false"
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
                        v-model="resource.data.description"
                      ></v-textarea>
                    </v-col>
                  </v-row>
                  <input type="hidden" name="code" :value="slugify(resource.data.name)">
                  <input type="hidden" name="user_id" :value="auth.id">
                </v-card>
              </v-card-text>

              <div class="d-flex mb-6">
                <v-divider></v-divider>
                <v-icon small color="muted" class="mx-3 mt-n2">mdi-book-open-outline</v-icon>
                <v-divider></v-divider>
              </div>
              <v-card-text class="pa-0">
                <h4 class="mb-5" v-text="trans('Courses')"></h4>
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
                  <v-card-text :key="item" class="error--text" v-html="item" v-for="item in errors"></v-card-text> -->
                </validation-provider>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-row>
              <v-col cols="12">
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
import Classroom from './Models/Classroom'
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
    CourseTreeview: () => import('./partials/CourseTreeview'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
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
    auth: $auth.getUser(),
    resource: new Classroom,
    isValid: true,
    search: '',
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

    load (val = true) {
      this.resource.loading = val
      this.loading = val
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

      axios.post(
        $api.update(this.resource.data.id),
        this.parseResourceData(this.resource.data)).then(response => {
        this.showSnackbar({
          text: trans('Classroom updated successfully'),
        })

        this.showSuccessbox({
          text: trans('Updated Classroom {name}', { name: this.resource.data.name }),
          buttons: {
            show: {
              code: 'classrooms.index',
              to: { name: 'classrooms.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Classrooms'),
            },
            create: {
              code: 'classrooms.create',
              to: { name: 'classrooms.create', query: {from: this.$route.fullPath} },
              icon: 'mdi-google-classroom',
              text: trans('Add New Classroom'),
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
      }).finally(() => { this.load(false) })
    },

    getCourse () {
      axios.get(
        $api.courses.list()
      ).then(response => {
        this.resource.data.courses = response.data.data
      }).finally(() => {
        this.getResource()
      })
    },

    getResource () {
      this.resource.loading = true
      axios.get(
        $api.show(this.$route.params.id), {
          params: { materials: true }
        }).then(response => {
        this.resource.data = response.data.data
        this.resource.data.selected = clone(this.resource.data.meta['courses:selected'])
      }).finally(() => {
        this.resource.loading = false
        this.resource.isPrestine = true
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
        this.resource.hasErrors = this.$refs.updateform.flags.invalid

        if (!this.resource.hasErrors && !this.isLoading) {
          this.hideAlertbox()
        }
      },
      deep: true,
    },

    'resource.data.selected': {
      handler (val) {
        this.resource.isPrestine = false
        this.resource.hasErrors = this.$refs.updateform.flags.invalid

        if (!this.resource.hasErrors) {
          this.hideAlertbox()
        }
      },
      deep: false,
    },
  },
}
</script>
