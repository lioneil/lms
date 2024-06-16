<template>
  <admin>
    <metatag :title="trans('Add User')"></metatag>
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
              <v-btn @click="askUserToDiscardUnsavedChanges" text class="ml-3 mr-0" large v-text="trans('Discard')"></v-btn>
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
                  :disabled="isFormDisabled"
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

    <validation-observer ref="addform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form :disabled="isLoading" ref="addform-form" autocomplete="false" v-on:submit.prevent="handleSubmit(submit($event))" enctype="multipart/form-data">
        <button ref="submit-button" type="submit" class="d-none"></button>
        <page-header :back="{ to: { name: 'users.index' }, text: trans('Users') }">
          <template v-slot:title><span v-text="trans('Add User')"></span></template>
        </page-header>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <v-row>
          <v-col cols="12" lg="9">
            <v-card flat color="transparent" class="mb-6">
              <v-card-title class="pa-0" v-text="trans('Account Information')"></v-card-title>
              <v-card-text class="pa-0">
                <v-row justify="space-between">
                  <v-col cols="6" md="2">
                    <v-select
                      :disabled="isLoading"
                      :items="['Mr.', 'Ms.', 'Mrs.']"
                      :label="trans('Prefix')"
                      append-icon="mdi-chevron-down"
                      background-color="selects"
                      class="dt-text-field"
                      dense
                      hide-details
                      outlined
                      v-model="resource.data.prefixname"
                    ></v-select>
                    <input type="hidden" name="prefixname" v-model="resource.data.prefixname">
                  </v-col>
                  <v-col cols="6" md="2">
                    <v-text-field :disabled="isLoading" hide-details :label="trans('Suffix')" class="dt-text-field" name="suffixname" outlined dense v-model="resource.data.suffixname"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="4">
                    <validation-provider vid="firstname" :name="trans('first name')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('First name')"
                        autofocus
                        class="dt-text-field"
                        name="firstname"
                        outlined
                        prepend-inner-icon="mdi-account-outline"
                        v-model="resource.data.firstname"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="4">
                    <validation-provider vid="middlename" :name="trans('middle name')" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('Middle name')"
                        class="dt-text-field"
                        name="middlename"
                        outlined
                        v-model="resource.data.middlename"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="4">
                    <validation-provider vid="lastname" :name="trans('last name')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('Last name')"
                        class="dt-text-field"
                        name="lastname"
                        outlined
                        v-model="resource.data.lastname"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
                    <birthday-picker v-model="resource.data.details['Birthday']"></birthday-picker>
                  </v-col>
                  <v-col cols="12" md="6">
                    <gender-picker
                      :items="resource.gender.items"
                      v-model="resource.data.details['Gender']"
                      >
                    </gender-picker>
                  </v-col>
                </v-row>
                <v-row align="center">
                  <v-col cols="12">
                    <validation-provider vid="details[Mobile Phone]" :name="trans('Mobile phone')" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('Mobile phone')"
                        class="dt-text-field"
                        name="details[Mobile Phone][value]"
                        outlined
                        prepend-inner-icon="mdi-cellphone-android"
                        v-model="resource.data.details['Mobile Phone'].value"
                        >
                      </v-text-field>
                    </validation-provider>
                    <input type="hidden" name="details[Mobile Phone][key]" :value="trans(resource.data.details['Mobile Phone'].key)">
                    <input type="hidden" name="details[Mobile Phone][icon]" :value="resource.data.details['Mobile Phone'].icon">
                  </v-col>
                  <!-- <v-col cols="12" md="6">
                    <marital-status-picker
                      :items="resource.maritalStatus.items"
                      v-model="resource.data.details['Marital Status']"
                      >
                    </marital-status-picker>
                  </v-col> -->
                </v-row>
                <v-row>
                  <v-col cols="12">
                    <validation-provider vid="details[Home Address]" :name="trans('Home address')" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('Home address')"
                        class="dt-text-field"
                        name="details[Home Address][value]"
                        outlined
                        prepend-inner-icon="mdi-map-marker"
                        v-model="resource.data.details['Home Address'].value"
                        >
                      </v-text-field>
                    </validation-provider>
                    <input type="hidden" name="details[Home Address][key]" :value="trans(resource.data.details['Home Address'].key)">
                    <input type="hidden" name="details[Home Address][icon]" :value="resource.data.details['Home Address'].icon">
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>

            <can code="password.change">
              <account-details password confirmed v-model="resource"></account-details>
            </can>

            <v-card flat color="transparent" class="mb-6">
              <v-card-title class="pa-0" v-text="trans('Additional Background Details')"></v-card-title>
              <v-card-text class="pa-0">
                <repeater :dense="isDense" :disabled="isLoading" v-model="resource.data.details.others"></repeater>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" lg="3">
            <v-row>
              <v-col cols="12">
                <v-card class="mb-6">
                  <v-card-title class="pb-0" v-text="trans('Photo')"></v-card-title>
                  <v-card-text class="text-center">
                    <upload-avatar name="photo" v-model="resource.data.avatar"></upload-avatar>
                  </v-card-text>
                </v-card>

                <role-picker :dense="isDense" :disabled="isLoading" class="mb-6" v-model="resource.data.roles"></role-picker>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-form>
    </validation-observer>
  </admin>
</template>

<script>
import $api from './routes/api'
import clone from 'lodash/clone'
import User from './Models/User'
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
    AccountDetails: () => import('./cards/AccountDetails'),
  },

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
      shortkeyCtrlIsPressed: 'shortkey/ctrlIsPressed',
      progressbar: 'progressbar/progressbar',
    }),
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
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
  },

  data: () => ({
    resource: new User,
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

    askUserToDiscardUnsavedChanges () {
      this.showDialog({
        illustration: () => import('@/components/Icons/WorkingDeveloperIcon.vue'),
        title: trans('Discard changes?'),
        text: trans('You have unsaved changes on this page. If you navigate away from this page, data will not be recovered.'),
        buttons: {
          cancel: {
            text: trans('Cancel'),
            callback: () => {
              this.hideDialog()
            },
          },
          action: {
            text: trans('Discard'),
            callback: () => {
              this.resource.isPrestine = true
              this.hideDialog()
              this.$router.replace({ name: 'users.index' })
            },
          },
        }
      })
    },

    parseResourceData (item) {
      let data = clone(item)
      let formData = new FormData(this.$refs['addform-form'].$el)

      data.details = Object.assign({}, data.details, data.details.others || {})
      delete data.details.others

      formData.append('username', data.username)
      formData.append('email', data.email)

      for (var i in data.details) {
        let c = data.details[i], key = c.key, icon = c.icon,
        value = c.value == undefined || c.value == 'undefined' ||
        c.value == 'null' || c.value == null ? '' : c.value

        formData.append(`details[${c.key}][key]`, key)
        formData.append(`details[${c.key}][icon]`, icon)
        formData.append(`details[${c.key}][value]`, value)
      }

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
      e.preventDefault()
      this.hideAlertbox()

      axios.post($api.store(), this.parseResourceData(this.resource.data), {
        headers: {'Content-Type': 'multipart/form-data'},
        onUploadProgress: (e) => {
          var progress = Math.round((e.loaded * 100) / e.total)
          this.setProgressStatus({ isUploading:true, progress: progress });
        },
      }).then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('User created successfully'),
        })

        this.$router.push({
          name: 'users.edit',
          params: {
            id: response.data.data.id
          },
          query: {
            success: true,
          },
        })

        this.showSuccessbox({
          text: trans('Updated User {name}', { name: response.data.data.displayname }),
          buttons: {
            show: {
              code: 'users.show',
              to: { name: 'users.show', params: { id: response.data.data.id } },
              icon: 'mdi-account-search-outline',
              text: trans('View Details'),
            },
            create: {
              code: 'users.create',
              to: { name: 'users.create' },
              icon: 'mdi-account-plus-outline',
              text: trans('Add New User'),
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
