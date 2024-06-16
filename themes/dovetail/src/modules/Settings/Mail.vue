<template>
  <admin>
    <metatag :title="trans('Mail Settings')"></metatag>

    <template v-slot:appbar>
      <v-container class="py-0 px-0">
        <v-row justify="space-between" align="center">
          <v-fade-transition>
            <v-col v-if="isNotFormPrestine" class="py-0" cols="auto">
              <v-toolbar-title class="muted--text" v-text="trans('Unsaved changes')"></v-toolbar-title>
            </v-col>
          </v-fade-transition>
          <v-spacer></v-spacer>
          <v-col class="py-0" cols="auto">
            <div class="d-flex justify-end">
              <v-spacer></v-spacer>
              <v-badge
                bordered
                bottom
                class="dt-badge"
                color="dark"
                content="s"
                offset-x="20"
                offset-y="20"
                tile
                transition="fade-transition"
                v-model="shortkeyCtrlIsPressed"
                >
                <v-btn
                  :loading="isLoading"
                  @click.prevent="submitForm"
                  @shortkey="submitForm"
                  class="ml-3 mr-0"
                  color="primary"
                  large
                  ref="submit-button-main"
                  type="submit"
                  v-shortkey.once="['ctrl', 's']"
                  >
                  <v-icon left>mdi-content-save-outline</v-icon>
                  <span v-text="trans('Save')"></span>
                </v-btn>
              </v-badge>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </template>

    <validation-observer ref="updateform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form :disabled="isLoading" ref="updateform-form" autocomplete="false" v-on:submit.prevent="handleSubmit(submit($event))" enctype="multipart/form-data">
        <button ref="submit-button" type="submit" class="d-none"></button>
        <page-header>
          <template v-slot:title>
            <span v-text="trans('Mail Settings')"></span>
          </template>
        </page-header>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <template v-if="isFetchingResource">
          <skeleton-edit></skeleton-edit>
        </template>

        <div v-show="isFinishedFetchingResource" class="mb-3">
          <v-row>
            <v-col cols="12" md="9">
              <v-row>
                <v-col cols="12">
                  <h3 v-text="trans('Mail Options')"></h3>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('From Name')"
                    class="dt-text-field"
                    hint="Specify the name that emails should be sent from."
                    name="mail:name"
                    outlined
                    persistent-hint
                    v-model="resource.data['mail:name']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('From Email')"
                    class="dt-text-field"
                    hint="Specify the email address that emails should be sent from."
                    name="mail:address"
                    outlined
                    persistent-hint
                    v-model="resource.data['mail:address']"
                    >
                  </v-text-field>
                </v-col>

                <v-col cols="12">
                  <h3 v-text="trans('SMTP Configuration')"></h3>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Driver')"
                    class="dt-text-field"
                    name="mail:driver"
                    outlined
                    hide-details
                    v-model="resource.data['mail:driver']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Host')"
                    class="dt-text-field"
                    name="mail:host"
                    outlined
                    hide-details
                    v-model="resource.data['mail:host']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Port')"
                    class="dt-text-field"
                    name="mail:port"
                    outlined
                    hide-details
                    v-model="resource.data['mail:port']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Username')"
                    class="dt-text-field"
                    name="mail:username"
                    outlined
                    hide-details
                    v-model="resource.data['mail:username']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Password')"
                    class="dt-text-field"
                    name="mail:password"
                    outlined
                    hide-details
                    type="password"
                    v-model="resource.data['mail:password']"
                    >
                  </v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    :dense="isDense"
                    :label="trans('Encryption')"
                    class="dt-text-field"
                    name="mail:encryption"
                    outlined
                    hide-details
                    v-model="resource.data['mail:encryption']"
                    >
                  </v-text-field>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </div>
      </v-form>
    </validation-observer>

    <v-card flat color="transparent" height="100"></v-card>
  </admin>
</template>

<script>
import $api from './routes/api'
import clone from 'lodash/clone'
import Mail from './Models/Mail'
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

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
    }),
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isInvalid () {
      return this.resource.isPrestine || this.resource.loading
    },
    isLoading () {
      return this.resource.loading
    },
    isFetchingResource () {
      return this.loading
    },
    isFinishedFetchingResource () {
      return !this.loading
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
  },

  components: {
    SkeletonEdit: () => import('./cards/SkeletonEdit'),
  },

  data: () => ({
    loading: true,
    resource: new Mail,
    isValid: true,
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

    load (val = true) {
      this.resource.loading = val
      this.loading = val
    },

    parseResourceData (item) {
      let data = clone(item)

      let formData = new FormData(this.$refs['updateform-form'].$el)

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
      this.$refs['submit-button'].click()
      window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth',
      })
    },

    submit (e) {
      this.load()
      this.hideAlertbox()
      e.preventDefault()

      axios.post(
        $api.save(),
        this.parseResourceData(this.resource.data),
      ).then(response => {
        this.showSnackbar({
          text: trans('Mail Settings updated successfully'),
        })

        // this.showSuccessbox({
        //   text: 'Mail Settings Updated'
        // })

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

    getResource () {
      axios.get(
        $api.list(), {
          params: {
            key: [
              'mail:name',
              'mail:address',
              'mail:driver',
              'mail:host',
              'mail:port',
              'mail:username',
              'mail:password',
              'mail:encryption'
            ]
          }
        }
      ).then(response => {
        this.resource.data = Object.assign([], this.resource.data, response.data)
      }).finally(() => {
        this.load(false)
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

        if (!this.resource.hasErrors) {
          this.hideAlertbox()
        }
      },
      deep: true,
    },
  },
}
</script>
