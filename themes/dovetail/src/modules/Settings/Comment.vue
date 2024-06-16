<template>
  <admin>
    <metatag :title="trans('Comment Settings')"></metatag>

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
            <span v-text="trans('Comment Settings')"></span>
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
            <v-col cols="12">
              <h3 v-text="trans('Comment Section')"></h3>
            </v-col>
            <v-col cols="12">
              <p>
                <span v-text="trans('Only applicable for Modules that support commenting.')"></span> <br/>
                <span v-text="trans('Gives roles an ability to comment through ')"></span>
                <router-link
                  class="primary--text t-d-none t-d-hover-lined"
                  exact
                  :to="{ name: 'roles.index' }"
                  link
                  v-text="trans('Roles and Permissions')"
                ></router-link>.
              </p>
              <v-checkbox
                :label="trans(`Enable comment section`)"
                :value="isCommentEnabled"
                @change="enableComment"
                hide-details
                class="mb-3"
              ></v-checkbox>
              <input type="hidden" ref="commenting" name="commenting:enable" v-model="resource.data['commenting:enable']">
            </v-col>
            <v-col cols="12">
              <h3 v-text="trans('Global Blacklisted Words')"></h3>
            </v-col>
            <v-col cols="12">
              <p>
                <span v-text="trans(`Words listed below will be banned from all available comments section.`)"></span> <br/>
                <span v-text="trans(`You may also specify blacklisted words for a certain modile from the module's own settings page.`)"></span>
              </p>
            </v-col>
            <v-col cols="12">
              <v-textarea
                auto-grow
                class="dt-text-field"
                hide-details
                label="List of blacklisted words"
                name="blacklisted:words"
                outlined
                v-model="resource.data['blacklisted:words']"
              ></v-textarea>
            </v-col>
            <v-col cols="12">
              <v-checkbox
                :label="trans('Check for exact word match only')"
                :value="isExactWord"
                @change="exactWord"
                class="mb-3"
                hide-details
              ></v-checkbox>
              <input type="hidden" ref="exact" name="blacklisted:exact" v-model="resource.data['blacklisted:exact']">
              <p>
                <span v-html="trans(`Note that if unchecked, the process may produce unintentional filtering, <br/> e.g ***ignment if the word <code>ass</code> is blacklisted.`)"></span>
              </p>
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
import Comment from './Models/Comment'
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
    isCommentEnabled () {
      if (this.resource.data['commenting:enable'] === '1')
        return true
      else
        return false
    },
    isExactWord () {
      if (this.resource.data['blacklisted:exact'] === '1')
        return true
      else
        return false
    },
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
    metaInfoCardList () {
      return [
        { icon: 'mdi-calendar', text: trans('Created :date', { date: this.resource.data.created }) },
        { icon: 'mdi-calendar-edit', text: trans('Modified :date', { date: this.resource.data.modified }) },
      ]
    },
  },

  components: {
    SkeletonEdit: () => import('./cards/SkeletonCommentEdit'),
  },

  data: () => ({
    loading: true,
    resource: new Comment,
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

    enableComment (e) {
      if(e)
        this.$refs.commenting.value = '1'
      else
        this.$refs.commenting.value = '0'
    },

    exactWord (e) {
      if(e)
        this.$refs.exact.value = '1'
      else
        this.$refs.exact.value = '0'
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
          text: trans('Comment Settings updated successfully'),
        })

        // this.showSuccessbox({
        //   text: 'Comment Settings Updated'
        // })

        this.$refs['updateform'].reset()
        this.getResource()
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
        $api.list(),
        {
          params: {
            'key': ['commenting:enable','blacklisted:words','blacklisted:exact']
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
