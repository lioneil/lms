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

    <page-header :back="{ to: { name: 'materials.index' }, text: trans('Back'), query: {from: $route.fullPath} }">
      <template v-slot:title>
        <span v-text="trans('Edit Material')"></span>
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

                <v-card flat color="transparent" v-show="isFinishedFetchingResource" class="mb-3">
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
                      <validation-provider
                        :name="trans('coursewareable_id')"
                        rules="required"
                        v-slot="{ errors }"
                        vid="coursewareable_id"
                        >
                        <v-autocomplete
                          :error-messages="errors"
                          :items="courses"
                          :label="trans('Select a course')"
                          append-icon="mdi-chevron-down"
                          background-color="selects"
                          class="dt-text-field"
                          item-text="title"
                          item-value="id"
                          name="coursewareable_id"
                          outlined
                          v-model="resource.data.coursewareable_id"
                        ></v-autocomplete>
                      </validation-provider>
                    </v-col>
                  </v-row>
                </v-card>
              </v-card-text>

              <!-- Material Component -->
              <material-content
                :has-old="true"
                :type="resource.data.type"
                v-model="resource.data.uri"
                v-show="isFinishedFetchingResource"
              ></material-content>
              <!-- Material Component -->
            </v-card>

            <input type="hidden" name="coursewareable_type" :value="resource.data.coursewareable_type">
            <input type="hidden" name="type" :value="resource.data.type">
            <input type="hidden" name="user_id" :value="auth.id">
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
  </admin>
</template>

<script>
import $api from '@/modules/Course/routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Material from './Models/Material'
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
    MaterialContent: () => import('./partials/MaterialContent'),
    SkeletonEdit: () => import('@/modules/Course/cards/SkeletonEdit'),
  },

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
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
    loading: true,
    resource: new Material,
    courses: [],
  }),

  methods: {
    ...mapActions({
      showAlertbox: 'alertbox/show',
      hideAlertbox: 'alertbox/hide',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
      hideSnackbar: 'snackbar/hide',
      setProgressStatus: 'progressbar/setProgressStatus',
      showProgressbar: 'progressbar/showProgressbar',
      hideProgressbar: 'progressbar/hideProgressbar',
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

    getCourseResource() {
      axios.get($api.list())
        .then(response => {
          this.courses = response.data.data
        })
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
        $api.material.update(this.resource.data.id),
        this.parseResourceData(this.resource.data), {
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
        this.showSnackbar({
          text: trans('Material updated successfully'),
        })

        this.$refs['updateform'].reset()
        this.resource.isPrestine = true

        this.$router.push({name: 'materials.index'});
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

    getResource: function () {
      this.resource.loading = true
      this.showProgressbar()
      axios.get(
        $api.material.show(this.$route.params.id)
      ).then(response => {
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
    this.getCourseResource()
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
