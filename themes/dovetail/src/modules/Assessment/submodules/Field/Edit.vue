<template>
  <admin>
    <metatag :title="`${assessment.data.title} - Edit Field`"></metatag>

    <template v-slot:appbar>
      <appbar-action-buttons
        :is-form-disabled="isFormDisabled"
        :is-loading="isLoading"
        :is-not-form-prestine="isNotFormPrestine"
        :shortkey-ctrl-is-pressed="shortkeyCtrlIsPressed"
        :submit-form="submitForm"
        router-name="assessments.index"
        v-model="resource"
        @item:submit="submitForm"
        >
        <template v-slot:text>
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'fields.index' }, text: trans('Back'), query: {from: $route.fullPath} }">
      <template v-slot:title>
        <span v-text="trans('Edit Field')"></span>
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
                <v-row>
                 <v-col cols="12">
                    <v-card :color="isDarkMode ? 'workspace' : 'white'" :class="{ 'dt-full-width': fullWidth }" flat>
                      <div :class="{ 'container': fullWidth }">
                        <v-card-title class="px-0">
                          <span v-text="trans(`Title`)"></span>
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
                            <span v-text="trans('Preview Title')"></span>
                          </v-tooltip>
                        </v-card-title>

                        <v-card-text class="pa-0">
                          <dt-editor :class="fullWidth ? 'fullwidth' : null" v-model="resource.data.title" name="title" :url="api.field.upload()"></dt-editor>
                          <!-- Preview -->
                          <v-dialog v-model="previewContent" fullscreen hide-overlay transition="dialog-bottom-transition">
                            <v-card flat tile>
                              <v-toolbar elevation="1" color="bar">
                                <v-container>
                                  <div class="d-flex justify-space-between align-center">
                                    <v-toolbar-title v-text="trans('Preview Title') "></v-toolbar-title>
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
                                    <div class="ck-content" v-html="resource.data.title"></div>
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
              </v-card-text>

              <!-- Field Component -->
              <component v-model="resource.data.metadata.items" :key="type" :is="type"></component>
              <!-- Field Component -->
            </v-card>
          </v-col>
        </v-row>
        <!-- Fields -->

        <input type="hidden" name="code" :value="new Date().getTime()">
        <input type="hidden" name="user_id" :value="auth.id">
        <input type="hidden" name="form_id" :value="assessment.data.id">
        <input type="hidden" name="group" value="">

        <!-- <input type="hidden" name="sort" :value="assessment.data.fields.length+1"> -->
        <!-- <input type="hidden" name="metadata[parent]" :value="parent"> -->
      </v-form>
    </validation-observer>

    <v-card flat color="transparent" height="100"></v-card>
  </admin>
</template>

<script>
import $api from '@/modules/Assessment/routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Field from './Models/Field'
import Assessment from '@/modules/Assessment/Models/Assessment'
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
    MultipleChoiceField: () => import('./partials/MultipleChoiceField'),
    CheckboxField: () => import('./partials/CheckboxField'),
    ShortAnswerField: () => import('./partials/ShortAnswerField'),
    EssayField: () => import('./partials/EssayField'),
    DropDownField: () => import('./partials/DropDownField'),
    LinearScaleField: () => import('./partials/LinearScaleField'),
    UnsupportedField: () => import('./partials/UnsupportedField'),
  },

  computed: {
    type () {
      if (this.$route.query.type in this.$options.components) {
        return this.$route.query.type
      }

      return 'UnsupportedField'
    },
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
    isDarkMode () {
      return this.$vuetify.theme.isDark;
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
    assessment: new Assessment,
    loading: true,
    resource: new Field,
    fullWidth: false,
    previewContent: false,
  }),

  methods: {
    ...mapActions({
      showAlertbox: 'alertbox/show',
      hideAlertbox: 'alertbox/hide',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
      hideSnackbar: 'snackbar/hide',
      showErrorbox: 'errorbox/show',
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

    getAssessmentResource () {
      this.resource.loading = true
      this.showProgressbar()
      axios.get(
        $api.show(this.$route.params.id)
      ).then(response => {
        this.assessment.data = response.data.data
      }).finally(() => {
        this.resource.loading = false,
        this.hideProgressbar()
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
      // # Shows the items error
      const items = this.resource.data.metadata.items

      if(items && items.length > 0) {
        let errors = {}
        let isBlank = false;
        let noAnswer = true;

        items.forEach(e => {
          if(e.text === '') isBlank = true
          if(e.answer === '1') noAnswer = false
        })

        if(isBlank) errors = { ...errors, text: ['The text is required'] }
        if(noAnswer) errors = { ...errors, answer: ['The answer is required'] }

        if(isBlank || noAnswer) {
          this.showErrorbox({
            text: trans('The given data was invalid.'),
            errors: errors,
          })
          return
        }
      }
      // # Shows the items error

      this.showProgressbar()
      this.hideAlertbox()
      e.preventDefault()

      axios.post(
        $api.field.update(this.resource.data.id),
        this.parseResourceData(this.resource.data), {
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
        this.showSnackbar({
          text: trans('Field updated successfully'),
        })

        this.$refs['updateform'].reset()
        this.resource.isPrestine = true

        this.$router.push({name: 'fields.index'});
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
        $api.field.show(this.$route.params.field)
      ).then(response => {
        this.resource.data = response.data.data
      }).finally(() => {
        this.resource.loading = false
        this.hideProgressbar()
        this.resource.isPrestine = true
      })
    },

    toggleFullWidth () {
      this.fullWidth = ! this.fullWidth
    },
  },

  mounted () {
    this.getResource()
    this.getAssessmentResource()
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
