<template>
  <admin>
    <metatag :title="trans(resource.data.name)"></metatag>

    <validation-observer ref="updateform" v-slot="{ handleSubmit, errors, invalid, passed }">
      <v-form :disabled="isLoading" ref="updateform-form" autocomplete="false" v-on:submit.prevent="handleSubmit(submit($event))">
        <button ref="submit-button" type="submit" class="d-none"></button>
        <page-header>
          <template v-slot:title>
            <span v-text="trans('Edit Widget Visibility')"></span>
          </template>
        </page-header>

        <!-- Alertbox -->
        <alertbox></alertbox>
        <!-- Alertbox -->

        <v-row>
          <v-col cols="12" md="8" class="mx-auto">
            <v-card>
              <v-card-title><v-icon class="mr-2" v-html="resource.data.file"></v-icon> <span v-text="trans(resource.data.name)"></span></v-card-title>
              <v-card-text>
                <p class="text-body-2 link--text ma-0" v-text="trans(resource.data.description)"></p>
                <p class="text-body-2 link--text ma-0"><b v-text="trans('Location:')"></b> <span v-text="`dashboard.2.3`"></span></p>

                <v-combobox
                  v-model="resource.data.selected"
                  :items="roles"
                  item-text="name"
                  item-value="text"
                  label="Roles"
                  multiple
                  chips
                  class="mt-10"
                  hint="Only the roles selected will be able to see this widget."
                  persistent-hint
                  >
                    <template v-slot:selection="{ attrs, item, parent, selected }">

                      <v-chip
                        v-if="item"
                        v-bind="attrs"
                        close
                        :input-value="resource.data.selected"
                        @click:close="parent.selectItem(item)"
                      >
                        <span class="pr-2" v-text="item.name "></span>
                      </v-chip>
                    </template>
                  </v-combobox>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" v-text="trans('Save')"></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </validation-observer>
  </admin>
</template>

<script>
import $api from './routes/api'
import Widget from './Models/Widget'
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

  data: () => ({
    api: $api,
    isValid: true,
    loading: true,
    resource: new Widget,
    roles: [],
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

    async getResource () {
      const result = await axios.get($api.role.list())
      this.roles = result.data.data
      axios.get(
        $api.show(this.$route.params.id)
      ).then(response => {
        this.resource = Object.assign([], this.resource.data, response.data)
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
