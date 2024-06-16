<template>
  <admin>
    <metatag :title="`${course.data.title} - Add Content`"></metatag>

    <template v-slot:appbar>
      <appbar-action-buttons
        :is-form-disabled="isFormDisabled"
        :is-loading="isLoading"
        :is-not-form-prestine="isNotFormPrestine"
        :shortkey-ctrl-is-pressed="shortkeyCtrlIsPressed"
        :submit-form="submitForm"
        router-name="contents.index"
        v-model="resource"
        @item:submit="submitForm"
        >
        <template v-slot:text>
          <span v-text="trans('Save')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'contents.index' }, text: trans('Back'), query: {from: $route.fullPath} }">
      <template v-slot:title><span v-text="course.data.title"></span></template>
    </page-header>

    <validation-observer ref="addform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form
        :disabled="isLoading"
        autocomplete="false"
        enctype="multipart/form-data"
        ref="addform-form"
        v-on:submit.prevent="handleSubmit(submit($event))"
        >
        <button ref="submit-button" type="submit" class="d-none"></button>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <!-- Fields -->
        <v-row>
          <v-col cols="12" md="9">
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
                    <v-textarea
                      :label="trans('Description')"
                      auto-grow
                      class="dt-text-field mb-6"
                      hide-details
                      name="description"
                      outlined
                      v-model="resource.data.description"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>

              <!-- Content Component -->
              <component v-model="resource.data.content" :key="type" :is="type"></component>
              <!-- Content Component -->
            </v-card>
          </v-col>
        </v-row>
        <!-- Fields -->

        <input type="hidden" name="slug" :value="slugify(resource.data.title)">
        <input type="hidden" name="user_id" :value="auth.id">
        <input type="hidden" name="course_id" :value="course.data.id">
        <input type="hidden" name="sort" :value="course.data.contents.length+1">
        <input type="hidden" name="metadata[parent]" :value="parent">
      </v-form>
    </validation-observer>

    <v-card flat color="transparent" height="100"></v-card>
  </admin>
</template>

<script>
import $api from '@/modules/Course/routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Content from './Models/Content'
import Course from '@/modules/Course/Models/Course'
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
    TextContent: () => import('./partials/TextContent'),
    UnsupportedContent: () => import('./partials/UnsupportedContent'),
    VideoContent: () => import('./partials/VideoContent'),
    PresentationContent: () => import('./partials/PresentationContent'),
    PDFContent: () => import('./partials/PDFContent'),
    EmbedContent: () => import('./partials/EmbedContent'),
    ScormContent: () => import('./partials/ScormContent'),
    AssignmentContent: () => import('./partials/AssignmentContent'),
    ExamContent: () => import('./partials/ExamContent'),
  },

  computed: {
    ...mapGetters({
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),

    type () {
      if (this.$route.query.type in this.$options.components) {
        return this.$route.query.type
      }

      return 'UnsupportedContent'
    },
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

    parent: {
      get () {
        let items = clone(this.course.data.contents);
        let len = items.length;

        items.reverse();

        for (var i = 0; i < len; i++) {
          let current = items[i];

          if (current.is_section) {
            return current.id
          }
        }
      },

      set (val) {
        this.$emit('parent', val);
      },
    },
  },

  data: () => ({
    auth: $auth.getUser(),
    course: new Course,
    resource: new Content,
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

    getCourseResource () {
      this.showProgressbar()
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.course.data = response.data.data
        }).finally(() => { this.hideProgressbar() })
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
        $api.content.store(), this.parseResourceData(this.resource.data), {
        headers: {'Content-Type': 'multipart/form-data'},
        onUploadProgress: (e) => {
          var progress = Math.round((e.loaded * 100) / e.total)
          this.setProgressStatus({
            isUploading: true,
            progress: progress,
          });
        },
      }).then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Content created successfully'),
        })

        this.$router.push({
          name: 'contents.index',
          params: {
            id: this.course.data.id
          },
          query: {
            success: true,
          },
        })
      }).catch(err => {
        this.form.setErrors(err.response.data.errors)
      }).finally(() => {
        this.hideProgressbar()
      })
    },
    },

  mounted () {
    this.getCourseResource()
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
