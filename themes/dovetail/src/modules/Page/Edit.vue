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
        router-name="pages.index"
        v-model="resource"
        >
        <template v-slot:text>
          <span v-text="trans('Update')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'pages.index' }, text: trans('Pages') }">
      <template v-slot:title>
        <span v-text="trans('Edit Page')"></span>
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
                          @input="createCode"
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
                      <validation-provider vid="code" :name="trans('code')" rules="required" v-slot="{ errors }">
                        <v-text-field
                          :append-icon="(codeLock ? 'mdi-lock-outline' : 'mdi-lock-open-outline')"
                          :error-messages="errors"
                          :label="trans('Code')"
                          :readonly="codeLock"
                          @click:append="toggleCodeLock"
                          autofocus
                          class="dt-text-field"
                          hint="Code is used in generating URL. To customize the code, toggle the lock icon on this field."
                          name="code"
                          outlined
                          persistent-hint
                          v-model="resource.data.code"
                          >
                        </v-text-field>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                      <validation-provider vid="template_id" :name="trans('template')" rules="required" v-slot="{ errors }">
                        <v-select
                          :error-messages="errors"
                          :items="templates"
                          append-icon="mdi-chevron-down"
                          background-color="selects"
                          class="dt-text-field"
                          item-text="name"
                          item-value="id"
                          label="Template"
                          name="template_id"
                          outlined
                          v-model="resource.data.template_id"
                        ></v-select>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12">
                        <category-picker class="mb-6" :url="categoryListUrl" v-model="resource.data.category_id"></category-picker>
                    </v-col>
                    <v-col cols="12">
                      <empty-content v-if="!resource.data.body">
                        <template v-slot:actions>
                          <v-btn
                            @click="toggleFullScreen()"
                            class="mb-10"
                            color="primary"
                            exact
                            large
                            >
                            <v-icon small left>mdi-file-outline</v-icon>
                            <span v-text="trans('Add Content')"></span>
                          </v-btn>
                        </template>
                      </empty-content>
                      <validation-provider v-else vid="body" :name="trans('content')" rules="required" v-slot="{ errors }">
                        <v-card>
                          <v-card-title class="d-flex justify-space-between"><span v-text="trans(`Content`)"></span><v-btn @click="toggleFullScreen()" icon><v-icon>mdi-pencil</v-icon></v-btn></v-card-title>
                          <v-card-text class="pa-3">
                            <div v-if="resource.data.body" class="iframe_container">
                              <iframe ref="iframe"class="page-iframe" width="100%" :srcdoc="page">Click edit to add content</iframe>
                            </div>
                            </v-card-text>
                          </v-card>
                        <input type="hidden" name="body" :value="resource.data.body">
                        <div class="error--text mt-2"><small v-text="errors.join()"></small></div>
                      </validation-provider>
                    </v-col>
                  </v-row>
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
                  <v-card-title class="pb-0" v-text="trans('Photo')"></v-card-title>
                  <v-card-text class="text-center">
                    <upload-avatar name="feature" avatar="image_old" v-model="resource.data.feature"></upload-avatar>
                  </v-card-text>
                </v-card>

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
    <page-editor
      @toggleFullScreen="toggleFullScreen"
      v-if="isFinishedFetchingResource"
      v-model="resource.data.body"
      v-show="fullscreenEditor"
      >
    </page-editor>
    <v-card flat color="transparent" height="100"></v-card>
    <back-to-top></back-to-top>
  </admin>
</template>

<script>
import $api from './routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Page from './Models/Page'
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
    PageEditor: () => import('./partials/PageEditor'),
    SkeletonEdit: () => import('./cards/SkeletonEdit'),
    SkeletonIcon: () => import('./cards/SkeletonIcon'),
    EmptyContent: () => import('./partials/EmptyContent')
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
    page () {
      if(!this.resource.data.body) return ''
      const { html, css } = JSON.parse(this.resource.data.body)
      let styles = `
        <style>
          ${css}
        </style>`

      const doc = `
        <html>
          <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css">
            <link rel="stylesheet" href="${window.location.origin}/theme/dist/css/app.css">
            ${styles}
          </head>
          <body>
            ${html}
          </body>
        </html>`

      return doc
    }
  },

  data: () => ({
    api: $api,
    auth: $auth.getUser(),
    resource: new Page,
    isValid: true,
    categoryListUrl: $api.category.list(),
    fullWidth: false,
    previewContent: false,
    templates: [
      {
        id: 1,
        name: 'Template 1'
      },
      {
        id: 2,
        name: 'Template 2'
      },
      {
        id: 3,
        name: 'Template 3'
      },
    ],
    codeLock: true,
    fullscreenEditor: false
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

    toggleFullScreen () {
      this.fullscreenEditor = !this.fullscreenEditor
      const body = document.body
      if(this.fullscreenEditor)
        body.classList.add('gjs-open')
      else
        body.classList.remove('gjs-open')
    },

    createCode (e) {
      const str = e.trim()
      if(this.codeLock)
        this.resource.data.code = str.toLowerCase().replace(/ /g, '-')
    },

    parseResourceData (item) {
      const fields = ['title', 'code', 'user_id', 'body', 'template_id', 'feature', 'category_id']
      let data = clone(item)

      // let formData = new FormData(this.$refs['updateform-form'].$el)

      // formData.append('_method', 'put')

      // for (var key of formData.keys()) {
      //   if(!fields.includes(key)) formData.delete(key);
      // }
      // data = formData

      // for (var key of data.keys()) {
      //   if(!fields.includes(key)) data.delete(key);
      // }

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
      // # Shows the body missing error
      if(!this.resource.data.body) {
        const errors = {
          body: ['The content is required.']
        }
        this.showErrorbox({
          text: trans('The given data was invalid.'),
          errors: errors,
        })
        return
      }
      // # Shows the body missing error

      this.showProgressbar()
      this.hideAlertbox()
      e.preventDefault()

      axios.put(
        $api.update(this.resource.data.id),
        this.parseResourceData(this.resource.data), {
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
        this.showSnackbar({
          text: trans('Page updated successfully'),
        })

        this.showSuccessbox({
          text: trans(`Updated Page ${this.resource.data.title}`),
          buttons: {
            show: {
              code: 'pages.index',
              to: { name: 'pages.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Pages'),
            },
            create: {
              code: 'pages.create',
              to: { name: 'pages.create', query: {from: this.$route.fullPath} },
              icon: 'mdi-bullhorn-outline',
              text: trans('Add New Page'),
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

    toggleCodeLock () {
      this.codeLock = !this.codeLock
      if(this.codeLock)
        this.createCode(this.resource.data.title)
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
