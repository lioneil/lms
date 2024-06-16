<template>
  <admin>
    <metatag :title="resource.data.displayname"></metatag>

    <page-header :back="{ to: { name: 'users.index' }, text: trans('Users') }">
      <template v-slot:title><span v-text="resource.data.displayname"></span></template>
      <template v-slot:utilities>
        <can code="users.edit">
          <router-link tag="a" class="dt-link text-decoration-none mr-6" exact :to="{name: 'users.edit'}">
            <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
            <span v-text="trans('Edit')"></span>
          </router-link>
        </can>
        <can code="users.destroy">
          <a href="#" @click.prevent="askUserToDestroyResource(resource)" class="dt-link text-decoration-none mr-6">
            <v-icon small class="mb-1">mdi-delete-outline</v-icon>
            <span v-text="trans('Deactivate')"></span>
          </a>
        </can>
      </template>
    </page-header>

    <template v-if="resource.loading">
      <skeleton-show></skeleton-show>
    </template>

    <template v-else>
      <v-card>
        <v-card-text>
          <user-account-detail-card
            v-model="resource.data"
            >
          </user-account-detail-card>
        </v-card-text>

        <v-divider></v-divider>

        <!-- Background Details -->
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th colspan="100%" class="text-left" v-text="trans('Background Details')"></th>
              </tr>
            </thead>
            <tbody v-show="backgroundDetailsIsNotEmpty">
              <tr v-for="(detail, i) in resource.data['details:common']">
                <td class="font-weight-bold">
                  <v-icon v-if="detail.icon == 'null'" small left>mdi-square-edit-outline</v-icon>
                  <v-icon v-else small left v-text="detail.icon"></v-icon>
                  <span v-text="trans(detail.key)"></span>
                </td>
                <td v-text="trans(detail.text)"></td>
              </tr>
              </tr>
            </tbody>
            <!-- empty state -->
            <tbody v-if="backgroundDetailsIsEmpty">
              <tr>
                <td class="muted--text font-italic" v-text="trans('No resource found')"></td>
              </tr>
            </tbody>
            <!-- empty state -->
          </template>
        </v-simple-table>
        <!-- Background Details -->

        <!-- Additional Background Details -->
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th colspan="100%" class="text-left" v-text="trans('Additional Background Details')"></th>
              </tr>
            </thead>

            <tbody v-show="additionalBackgroundDetailsIsNotEmpty">
              <tr v-for="(detail, i) in resource.data['details:others']">
                <td class="font-weight-bold">
                  <v-icon small left>mdi-square-edit-outline</v-icon>
                  <span v-text="trans(detail.key)"></span>
                </td>
                <td v-text="trans(detail.text)"></td>
              </tr>
              </tr>
            </tbody>

            <!-- empty state -->
            <tbody v-if="additionalBackgroundDetailsIsEmpty">
              <tr>
                <td class="muted--text font-italic" v-text="trans('No resource found')"></td>
              </tr>
            </tbody>
            <!-- empty state -->
          </template>
        </v-simple-table>
        <!-- Additional Background Details -->
      </v-card>
    </template>
  </admin>
</template>

<script>
import $api from './routes/api'
import $auth from '@/core/Auth/auth'
import isEmpty from 'lodash/isEmpty'
import { mapActions } from 'vuex'

export default {
  components: {
    SkeletonShow: () => import('./cards/SkeletonShow'),
  },

  data: () => ({
    api: $api,
    auth: $auth.getUser(),

    resource: {
      loading: true,
      data: {
        displayname: '',
        username: ''
      },
    }
  }),

  computed: {
    backgroundDetailsIsEmpty () {
      return isEmpty(this.resource.data['details:common'])
    },

    backgroundDetailsIsNotEmpty () {
      return !this.backgroundDetailsIsEmpty
    },

    additionalBackgroundDetailsIsEmpty () {
      return isEmpty(this.resource.data['details:others'])
    },

    additionalBackgroundDetailsIsNotEmpty () {
      return !this.additionalBackgroundDetailsIsEmpty
    },
  },

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      errorDialog: 'dialog/error',
      showSnackbar: 'snackbar/show',
      loadDialog: 'dialog/loading',
    }),

    getResource () {
      this.resource.loading = true
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.resource.data = response.data.data
        }).finally(() => { this.resource.loading = false })
    },

    askUserToDestroyResource (resource) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon.vue'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: trans('You are about to deactivate the selected user.'),
        text: [trans('The user will be signed out from the app. Some data related to the account like comments and files will still remain.'), trans('Are you sure you want to deactivate and move :name to Trash?', {name: resource.data.displayname})],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: trans('Move to Trash'),
            color: 'warning',
            callback: (dialog) => {
              this.loadDialog(true)
              this.destroyResource(resource)
            }
          }
        }
      })
    },

    destroyResource (item) {
      item.loading = true
      axios.delete(
        $api.destroy(item.data.id)
      ).then(response => {
        this.hideDialog()
        this.showSnackbar({
          show: true,
          text: trans_choice('User successfully deactivated', 1)
        })
        this.$router.push({ name: 'users.index' })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => { item.loading = false })
    },
  },

  mounted () {
    this.getResource()
  },
}
</script>
