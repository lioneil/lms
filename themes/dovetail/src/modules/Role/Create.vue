<template>
  <admin>
    <metatag :title="trans('Add Role')"></metatag>

    <template v-slot:appbar>
      <v-container class="py-0 px-0">
        <v-row justify="space-between" align="center">
          <v-fade-transition>
            <v-col v-if="isNotFormPrestine" class="py-0" cols="auto">
              <v-toolbar-title class="muted--text">{{ trans('Unsaved changes') }}</v-toolbar-title>
            </v-col>
          </v-fade-transition>
          <v-spacer></v-spacer>
          <v-col class="py-0" cols="auto">
            <div class="d-flex justify-end">
              <v-spacer></v-spacer>
              <v-btn @click="askUserToDiscardUnsavedChanges" text class="ml-3 mr-0" large>{{ trans('Discard') }}</v-btn>
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
                  {{ trans('Save') }}
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
        <page-header :back="{ to: { name: 'roles.index' }, text: trans('Roles') }">
          <template v-slot:title>{{ trans('Add Role') }}</template>
        </page-header>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <v-row>
          <v-col cols="12" md="9">
            <v-card flat color="transparent" class="mb-3">
              <v-card-text class="pa-0">
                <v-row>
                  <v-col cols="12">
                    <validation-provider vid="name" :name="trans('name')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
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
                    <validation-provider vid="code" rules="required" :name="trans('code')" v-slot="{ errors }">
                      <v-text-field
                        :dense="isDense"
                        :disabled="isLoading"
                        :error-messages="errors"
                        :label="trans('Code')"
                        :value="slugify(resource.data.name)"
                        class="dt-text-field"
                        name="code"
                        outlined
                      ></v-text-field>
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
                </v-row>
              </v-card-text>
              <div class="d-flex mb-6">
                <v-divider></v-divider>
                <v-icon small color="muted" class="mx-3 mt-n2">mdi-shield-lock</v-icon>
                <v-divider></v-divider>
              </div>
              <v-card-text class="pa-0">
                <h4 class="mb-5">{{ trans('Permissions') }}</h4>
                <treeview-field v-model="search"></treeview-field>
                <treeview
                  :items="resource.permissions"
                  :search="search"
                  :selectable="true"
                  v-model="resource.selected"
                ></treeview>

                <input type="hidden" v-for="item in resource.selected" name="permissions[]" :value="item">
                <validation-provider vid="permissions" name="permissions" rules="required" v-slot="{ errors }">
                  <v-card-text :key="item" class="error--text" v-html="item" v-for="item in errors"></v-card-text>
                </validation-provider>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </validation-observer>
  </admin>
</template>

<script>
import $api from './routes/api'
import clone from 'lodash/clone'
import Role from './Models/Role'
import { mapActions, mapGetters } from 'vuex'

export default {
  beforeRouteLeave (to, from, next) {
    if (this.isFormPrestine) {
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
    resource: new Role,
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
              this.$router.replace({ name: 'roles.index' })
            },
          },
        }
      })
    },

    load (val = true) {
      this.resource.loading = val
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

    displayPermissionsList () {
      axios.get(
        $api.permissions.list()
      ).then(response => {
        this.resource.permissions = Object.assign([], this.resource.permissions, response.data)
      })
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
      e.preventDefault()
      this.hideAlertbox()

      axios.post(
        $api.store(), this.parseResourceData(this.resource.data)).then(response => {
        this.resource.isPrestine = true

        this.showSnackbar({
          text: trans('Role created successfully'),
        })

        this.$router.push({
          name: 'roles.edit',
          params: {
            id: response.data.id
          },
          query: {
            success: true,
          },
        })

        this.showSuccessbox({
          text: trans('Created Role {name}', { name: response.data.name }),
          buttons: {
            show: {
              code: 'roles.show',
              to: { name: 'roles.show', params: { id: response.data.id } },
              icon: 'mdi-open-in-new',
              text: trans('View Details'),
            },
            create: {
              code: 'roles.create',
              to: { name: 'roles.create' },
              icon: 'mdi-shield-plus-outline',
              text: trans('Add New Role'),
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
    this.displayPermissionsList()
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
