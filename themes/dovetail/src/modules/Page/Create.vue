<template>
  <admin>
    <metatag :title="trans('Add Page')"></metatag>

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
          <span v-text="trans('Save')"></span>
        </template>
      </appbar-action-buttons>
    </template>

    <page-header :back="{ to: { name: 'pages.index' }, text: trans('Pages') }">
      <template v-slot:title>
        <span v-text="trans('Add Page')"></span>
      </template>
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
                        @input="createCode"
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
                            <iframe ref="iframe" class="page-iframe" width="100%" :srcdoc="page">Click edit to add content</iframe>
                          </div>
                          </v-card-text>
                        </v-card>
                      <input type="hidden" name="body" :value="resource.data.body">
                      <div class="error--text mt-2"><small v-text="errors.join()"></small></div>
                    </validation-provider>
                  </v-col>
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
                      <upload-avatar :loading="isLoading" name="feature" avatar="image_old" v-model="resource.data.feature"></upload-avatar>
                      <div class="error--text mt-2"><small v-text="errors.join()"></small></div>
                    </validation-provider>
                  </v-card-text>
                </v-card>
                <v-card class="mb-6">
                  <v-card-title v-text="trans('Draft')"></v-card-title>
                  <v-card-text class="text-center">
                    <v-switch
                      v-model="draft"
                      :label="draft ? `undraft` : `draft`"
                    ></v-switch>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <input type="hidden" name="published_at" value="" />
      </v-form>
    </validation-observer>
    <page-editor v-show="fullscreenEditor" v-model="resource.data.body" @toggleFullScreen="toggleFullScreen"></page-editor>
    <v-card flat color="transparent" height="100"></v-card>
    <back-to-top></back-to-top>
  </admin>
</template>

<script>
import $api from './routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Page from './Models/Page'
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
    PageEditor: () => import('./partials/PageEditor'),
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
    fullscreenEditor: false,
    draft: false
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
      showErrorbox: 'errorbox/show',
      setProgressStatus: 'progressbar/setProgressStatus',
      hideProgressbar: 'progressbar/hideProgressbar',
      showProgressbar: 'progressbar/showProgressbar',
    }),

    toggleFullScreen () {
      this.fullscreenEditor = !this.fullscreenEditor
      const body = document.body
      if(this.fullscreenEditor)
        body.classList.add('gjs-open')
      else
        body.classList.remove('gjs-open')
    },

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
      const fields = ['title', 'code', 'user_id', 'body', 'template_id', 'feature', 'category_id', 'published_at', 'drafted_at']
      let data = clone(item)

      let formData = new FormData(this.$refs['addform-form'].$el)

      for (var key of formData.keys()) {
        if(!fields.includes(key)) formData.delete(key);
      }
      data = formData

      for (var key of data.keys()) {
        if(!fields.includes(key)) data.delete(key);
      }

      return data
    },

    createCode (e) {
      const str = e.trim()
      if(this.codeLock)
        this.resource.data.code = str.toLowerCase().replace(/ /g, '-')
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

      axios.post(
        $api.store(), this.parseResourceData(this.resource.data), {
          headers: {'Content-Type': 'multipart/form-data'},
          onUploadProgress: (e) => {
            var progress = Math.round((e.loaded * 100) / e.total)
            this.setProgressStatus({ isUploading:true, progress: progress });
          },
        }).then(response => {
          if(this.draft) {
            axios.post($api.draft(response.data.id))
            .catch(err => {
              console.log(err)
            })
          }
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Page created successfully'),
        })

        this.$router.push({
          name: 'pages.edit',
          params: {
            id: response.data.id
          },
          query: {
            success: true,
          },
        })

        this.showSuccessbox({
          text: trans(`Created Page ${response.data.title}`),
          buttons: {
            show: {
              code: 'pages.index',
              to: { name: 'pages.index' },
              icon: 'mdi-open-in-new',
              text: trans('View All Pages'),
            },
            create: {
              code: 'pages.create',
              to: { name: 'pages.create' },
              icon: 'mdi-bullhorn-outline',
              text: trans('Add New Page'),
            },
          },
        })
      }).catch(err => {
        // # Shows the title instead of a slug on error
        if(err.response.data.errors.slug) {
          delete err.response.data.errors.title
          var newString = err.response.data.errors.slug[0].replace(/slug/g, "title")
          err.response.data.errors.slug[0] = newString
        }
        // # Shows the title instead of a slug on error

        this.form.setErrors(err.response.data.errors)
      }).finally(() => {
        this.hideProgressbar()
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
